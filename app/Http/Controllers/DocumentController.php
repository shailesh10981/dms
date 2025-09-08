<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Department;
use App\Models\Location;
use App\Models\Project;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Notifications\DocumentApprovalRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('document_view');

        $query = Document::with(['department', 'location', 'project', 'uploader'])
            ->whereNull('parent_id') // ✅ Only parent documents
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('document_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // For non-admin users, restrict to their department
        if (!auth()->user()->hasRole('admin')) {
            $query->where('department_id', auth()->user()->department_id);
        }

        $documents = $query->paginate(20);

        $departments = Department::all();
        $locations = Location::all();

        return view('documents.index', compact('documents', 'departments', 'locations'));
    }


    public function create()
    {
        $this->authorize('document_upload');

        $departments = Department::all();
        $locations = Location::all();
        $projects = Project::where('department_id', auth()->user()->department_id)->get();
        $users = User::select('id','name')->where('department_id', auth()->user()->department_id)->get();

        return view('documents.create', compact('departments', 'locations', 'projects', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('document_upload');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'project_id' => 'nullable|exists:projects,id',
            'document' => 'required|file|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
            'visibility' => 'required|in:Private,Public,Publish',
            'approver_ids' => 'nullable|array',
            'approver_ids.*' => 'exists:users,id',
        ]);

        // Handle file upload
        $file = $request->file('document');
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            . '-' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $fileName);

        // Create document - document_id will be auto-generated in the model's creating event
        $document = Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'department_id' => $validated['department_id'],
            'location_id' => $validated['location_id'],
            'project_id' => $validated['project_id'],
            'uploaded_by' => auth()->id(),
            'status' => 'draft',
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'expiry_date' => $validated['expiry_date'],
            'visibility' => $validated['visibility'],
            'workflow_definition' => $request->input('approver_ids', []),
        ]);

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Document uploaded successfully!');
    }

    public function show(Document $document)
    {
        $this->authorize('document_view');

        // Get the root document (original version)
        $rootDocument = $document;
        while ($rootDocument->parent) {
            $rootDocument = $rootDocument->parent;
        }

        // Load all versions in order with relationships
        $versions = Document::with(['uploader', 'approver'])
            ->where(function ($query) use ($rootDocument) {
                $query->where('id', $rootDocument->id)
                    ->orWhere('parent_id', $rootDocument->id);
            })
            ->orderBy('version')
            ->get();

        // Prepare version comparison data
        $changes = [];
        if ($document->parent) {
            $changes = [
                'title' => $document->title != $document->parent->title
                    ? ['old' => $document->parent->title, 'new' => $document->title]
                    : null,
                'description' => $document->description != $document->parent->description
                    ? ['old' => $document->parent->description, 'new' => $document->description]
                    : null,
                'file' => $document->file_name != $document->parent->file_name
                    ? ['old' => $document->parent->file_name, 'new' => $document->file_name]
                    : null
            ];
            $changes = array_filter($changes); // Remove unchanged fields
        }

        // Load document with all relationships
        $document->load([
            'department',
            'location',
            'project',
            'approvals.approver',
            'auditLogs' => function ($query) {
                $query->latest()->limit(10);
            }
        ]);

        return view('documents.show', [
            'document' => $document,
            'versions' => $versions,
            'changes' => $changes,
            'rootDocument' => $rootDocument
        ]);
    }
    public function edit(Document $document)
    {
        $this->authorize('document_edit');

        // Only allow editing if document is in draft status
        if ($document->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft documents can be edited.');
        }

        // Ensure user can only edit their own documents
        if ($document->uploaded_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $departments = Department::all();
        $locations = Location::all();
        $projects = Project::where('department_id', auth()->user()->department_id)->get();
        $users = User::select('id','name')->where('department_id', auth()->user()->department_id)->get();

        return view('documents.edit', compact('document', 'departments', 'locations', 'projects', 'users'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('document_edit');

        // Only allow updating if document is in draft status
        if ($document->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft documents can be edited.');
        }

        // Ensure user can only edit their own documents
        if ($document->uploaded_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'location_id' => 'nullable|exists:locations,id',
            'project_id' => 'nullable|exists:projects,id',
            'document' => 'nullable|file|max:10240', // 10MB max
            'expiry_date' => 'nullable|date|after:today',
            'visibility' => 'required|in:Private,Public,Publish',
            'approver_ids' => 'nullable|array',
            'approver_ids.*' => 'exists:users,id',
        ];

        // Handle file update if new file is uploaded
        if ($request->hasFile('document')) {
            // Delete old file
            Storage::delete($document->file_path);

            $file = $request->file('document');
            $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                . '-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('documents', $fileName);

            $document->file_path = $filePath;
            $document->file_name = $file->getClientOriginalName();
            $document->file_type = $file->getClientMimeType();
            $document->file_size = $file->getSize();
        }

        $document->title = $validated['title'];
        $document->description = $validated['description'];
        $document->department_id = $validated['department_id'];
        $document->location_id = $validated['location_id'];
        $document->project_id = $validated['project_id'];
        $document->expiry_date = $validated['expiry_date'];
        $document->visibility = $validated['visibility'];
        $document->workflow_definition = $request->input('approver_ids', []);
        $document->save();

        // Log the action
        $document->logAction('update', 'Document metadata updated');

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Document updated successfully!');
    }

    public function destroy(Document $document)
    {
        $this->authorize('document_delete');

        // Only allow deletion if document is in draft status
        if ($document->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft documents can be deleted.');
        }

        // Ensure user can only delete their own documents
        if ($document->uploaded_by != auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Soft delete the document
        $document->delete();

        // Log the action
        $document->logAction('delete', 'Document moved to trash');

        return redirect()->route('documents.index')
            ->with('success', 'Document moved to trash successfully!');
    }

    public function submitForApproval(Document $document)
    {
        $this->authorize('submitForApproval', $document);

        // Get department manager
        $manager = User::role('manager')
            ->where('department_id', $document->department_id)
            ->firstOrFail();

        // Determine approver chain
        $flow = is_array($document->workflow_definition) ? $document->workflow_definition : [];
        $firstApproverId = $flow[0] ?? optional(User::role('manager')->where('department_id', $document->department_id)->first())->id;

        $document->update([
            'status' => 'submitted',
            'current_approver_id' => $firstApproverId
        ]);

        // Create approval record for the current approver
        if ($firstApproverId) {
            $document->approvals()->create([
                'approver_id' => $firstApproverId,
                'status' => 'pending'
            ]);
        }

        // Log the action
        $document->logAction('submit', 'Document submitted for approval');

        // Notify current approver
        if ($firstApproverId) {
            User::find($firstApproverId)?->notify(new DocumentApprovalRequest($document));
        }

        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Document submitted for approval successfully!');
    }

    public function approve(Document $document)
    {
        $this->authorize('approve', $document);

        $document->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        // Update approval record
        $document->approvals()
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_at' => now()
            ]);

        // Log the action
        $document->logAction('approve', 'Document approved');

        // Send notification to uploader
        $document->uploader->notify(new DocumentApproved($document));

        return redirect()->route('approvals.index')
            ->with('success', 'Document approved successfully!');
    }
    public function download(Document $document)
    {
        $this->authorize('document_view');

        // For non-admin users, ensure they can only download documents from their department
        if (!auth()->user()->hasRole('admin') && $document->department_id != auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        // Log the download action
        $document->logAction('download', 'Document downloaded');

        return Storage::download($document->file_path, $document->file_name);
    }

    public function preview(Document $document)
    {
        $this->authorize('document_view');

        // For non-admin users, ensure they can only preview documents from their department
        if (!auth()->user()->hasRole('admin') && $document->department_id != auth()->user()->department_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        // Only allow preview for certain file types
        $previewableTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($document->file_type, $previewableTypes)) {
            return redirect()->route('documents.download', $document->id);
        }

        // Log the preview action
        $document->logAction('preview', 'Document previewed');

        $file = Storage::get($document->file_path);
        $response = response($file, 200);
        $response->header('Content-Type', $document->file_type);
        return $response;
    }

    public function trash()
    {
        $this->authorize('document_view');

        $query = Document::onlyTrashed()
            ->with(['department', 'uploader'])
            ->latest('deleted_at');

        // For non-admin users, restrict to their department
        if (!auth()->user()->hasRole('admin')) {
            $query->where('department_id', auth()->user()->department_id);
        }

        $documents = $query->paginate(20);

        return view('documents.trash', compact('documents'));
    }

    public function restore($id)
    {
        $this->authorize('document_restore');

        $document = Document::onlyTrashed()->findOrFail($id);

        // For non-admin users, ensure they can only restore their own documents
        if (!auth()->user()->hasRole('admin') && $document->uploaded_by != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $document->restore();

        // Log the action
        $document->logAction('restore', 'Document restored from trash');

        return redirect()->route('documents.trash')
            ->with('success', 'Document restored successfully!');
    }

    public function forceDelete($id)
    {
        $this->authorize('document_delete');

        $document = Document::onlyTrashed()->findOrFail($id);

        // Only admin can permanently delete
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the file
        Storage::delete($document->file_path);

        // Permanently delete the document
        $document->forceDelete();

        return redirect()->route('documents.trash')
            ->with('success', 'Document permanently deleted!');
    }

    public function showVersions(Document $document)
    {
        $this->authorize('document_view', $document);

        $document->load(['versions' => function ($query) {
            $query->orderBy('version', 'desc');
        }]);

        return view('documents.versions', compact('document'));
    }

    public function showCreateVersionForm(Document $document)
    {
        $this->authorize('document_upload');

        if (!$document->isLatestVersion()) {
            return redirect()->route('documents.show', $document->id)
                ->with('error', 'You can only create new versions from the latest version.');
        }

        return view('documents.create-version', compact('document'));
    }

    public function createNewVersion(Request $request, Document $document)
    {
        $this->authorize('document_upload');

        if (!$document->isLatestVersion()) {
            return back()->with('error', 'You can only create versions from the latest document version.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png|max:10240',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Handle file upload
            $file = $request->file('document');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($originalName) . '-' . time() . '.' . $extension;
            $filePath = $file->storeAs('documents', $fileName);

            // ✅ Extract prefix from parent document_id
            $parts = explode('-', $document->document_id);
            $prefix = implode('-', array_slice($parts, 0, 3)); // e.g., DOC-FIN-HQ
            $date = now()->format('Ymd');

            // ✅ Try multiple times to generate a unique document_id
            $maxTries = 10;
            $attempt = 0;
            $versionNumber = 2;

            do {
                $newDocumentId = "{$prefix}-{$date}-" . str_pad($versionNumber, 4, '0', STR_PAD_LEFT);

                if (!Document::where('document_id', $newDocumentId)->exists()) {
                    break;
                }

                $versionNumber++;
                $attempt++;

                if ($attempt >= $maxTries) {
                    throw new \Exception("Unable to generate a unique document ID after $maxTries attempts.");
                }
            } while (true);

            // ✅ Create new document version
            $newVersion = new Document([
                'parent_id' => $document->parent_id ?? $document->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? $document->description,
                'department_id' => $document->department_id,
                'location_id' => $document->location_id,
                'project_id' => $document->project_id,
                'uploaded_by' => auth()->id(),
                'status' => 'draft',
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'expiry_date' => $document->expiry_date,
                'version' => $versionNumber,
                'document_id' => $newDocumentId,
            ]);

            $newVersion->save();

            // ✅ Update status if rejected
            if ($document->status === 'rejected') {
                $document->status = 'resubmitted';
                $document->save();
            }

            // ✅ Log the action
            $newVersion->logAction('version_create', [
                'parent_version' => $document->version,
                'parent_id' => $document->id,
                'changes' => [
                    'title' => $validated['title'] != $document->title ? 'changed' : 'same',
                    'file' => 'new file uploaded',
                ],
            ]);

            if ($document->status === 'resubmitted') {
                $newVersion->logAction('resubmission', [
                    'reason' => $document->rejection_reason,
                    'rejected_by' => $document->approver->name ?? 'system',
                    'rejected_at' => $document->updated_at,
                ]);
            }

            DB::commit();

            return redirect()->route('documents.show', $newVersion->id)
                ->with('success', "New version (v{$versionNumber}) created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            Log::error('Version creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'parent_id' => $document->id,
                'error' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Version creation failed: ' . $e->getMessage());
        }
    }



    /**
     * Log detailed resubmission information
     */
    protected function logResubmission(Document $parent, Document $newVersion, array $validated)
    {
        $logData = [
            'message' => 'Resubmitted after rejection',
            'rejection_reason' => $parent->rejection_reason,
            'rejected_by' => $parent->approver->name ?? 'System',
            'rejected_at' => $parent->updated_at->format('Y-m-d H:i:s'),
            'resubmitted_by' => auth()->user()->name,
            'changes_summary' => $validated['change_summary'] ?? 'Not provided'
        ];

        $newVersion->logAction('resubmission', $logData);
        $parent->logAction('resubmission_created', ['new_version_id' => $newVersion->id]);
    }

    /**
     * Compare and log changes between versions
     */
    protected function logVersionChanges(Document $parent, Document $newVersion, array $validated)
    {
        $changes = [];

        // Compare title
        if ($parent->title !== $validated['title']) {
            $changes['title'] = [
                'from' => $parent->title,
                'to' => $validated['title']
            ];
        }

        // Compare description
        if (($validated['description'] ?? null) !== $parent->description) {
            $changes['description'] = [
                'from' => $parent->description,
                'to' => $validated['description'] ?? 'Removed'
            ];
        }

        // File is always considered changed in new version
        $changes['file'] = [
            'from' => $parent->file_name,
            'to' => $newVersion->file_name
        ];

        // Only log if there are changes
        if (!empty($changes)) {
            $newVersion->logAction('version_changes', [
                'parent_version' => $parent->version,
                'changes' => $changes
            ]);
        }
    }
}
