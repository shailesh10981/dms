<?php

namespace App\Http\Controllers;


use App\Models\Document;
use App\Models\DocumentApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\DocumentApproved;
use App\Notifications\DocumentRejected;
use Illuminate\Support\Facades\Log;

class DocumentApprovalController extends Controller
{
    public function index()
    {
        $this->authorize('document_approve');
        // abort_unless(auth()->user()->hasPermissionTo('document_approve'), 403);

        // Get pending approvals for the current manager
        $approvals = DocumentApproval::with(['document.department', 'document.uploader'])
            ->where('approver_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('approvals.index', compact('approvals'));
    }

    public function approve(Document $document)
    {



        $this->authorize('approve', $document);
        // Add immediate debug before changes
        Log::debug('Before approval', [
            'document_status' => $document->status,
            'approvals' => $document->approvals->toArray()
        ]);
        if (!in_array($document->status, ['submitted', 'resubmitted']) || $document->current_approver_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Update document status
        $document->status = 'approved';
        $document->save();
        Log::debug('After approval', [
            'document_status' => $document->refresh()->status,
            'approvals' => $document->refresh()->approvals->toArray()
        ]);
        // Update approval record
        $approval = $document->approvals()->where('status', 'pending')->first();
        $approval->status = 'approved';
        $approval->approved_at = now();
        $approval->save();

        // Delete all previous versions upon final approval
        $root = $document;
        while ($root->parent) { $root = $root->parent; }
        $previous = Document::where(function($q) use ($root) {
                $q->where('parent_id', $root->id)->orWhere('id', $root->id);
            })
            ->where('id', '!=', $document->id)
            ->get();
        foreach ($previous as $old) {
            \Illuminate\Support\Facades\Storage::delete($old->file_path);
            $old->forceDelete();
        }

        // Log the action
        $document->logAction('approve', 'Document approved; previous versions purged');
        $document->uploader->notify(new DocumentApproved($document));

        return redirect()->route('approvals.index')
            ->with('success', 'Document approved successfully and previous versions deleted!');
    }

    public function reject(Request $request, Document $document)
    {
        $this->authorize('reject', $document);

        $request->validate([
            'comments' => 'required|string|max:500',
        ]);

        // ✅ Allow rejecting both submitted and resubmitted documents
        if (!in_array($document->status, ['submitted', 'resubmitted']) || $document->current_approver_id != Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // ✅ Force document status to 'rejected'
        $document->status = 'rejected';
        $document->rejection_reason = $request->comments;
        $document->save();

        // ✅ Update approval record (optional safety check)
        $approval = $document->approvals()->where('status', 'pending')->first();
        if ($approval) {
            $approval->status = 'rejected';
            $approval->comments = $request->comments;
            $approval->save();
        }

        // ✅ Log the rejection
        $document->logAction('reject', 'Document rejected: ' . $request->comments);
        $document->uploader->notify(new DocumentRejected($document, $request->comments));

        return redirect()->route('approvals.index')
            ->with('success', 'Document rejected successfully!');
    }

    public function history()
    {
        $this->authorize('document_view');

        // Get all approvals for the current user (both as approver and uploader)
        $approvals = DocumentApproval::with(['document.department', 'document.uploader', 'approver'])
            ->where(function ($query) {
                $query->where('approver_id', Auth::id())
                    ->orWhereHas('document', function ($q) {
                        $q->where('uploaded_by', Auth::id());
                    });
            })
            ->where('status', '!=', 'pending')
            ->latest()
            ->paginate(20);

        return view('approvals.history', compact('approvals'));
    }
}
