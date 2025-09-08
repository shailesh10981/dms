<?php

namespace App\Http\Controllers;

use App\Models\RiskReport;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RiskReportController extends Controller
{
    protected array $fieldsByType = [
        'operational' => ['process', 'impact', 'likelihood', 'mitigation'],
        'compliance' => ['regulation', 'deadline', 'compliance_status', 'owner'],
        'financial' => ['amount', 'currency', 'exposure', 'probability'],
        'security' => ['asset', 'threat', 'vulnerability', 'control'],
    ];

    public function index(Request $request)
    {
        $reports = RiskReport::with(['department', 'creator'])
            ->when($request->issue_type, fn($q, $t) => $q->where('issue_type', $t))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);
        return view('risk.reports.index', compact('reports'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('risk.reports.create', [
            'departments' => $departments,
            'fieldsByType' => $this->fieldsByType,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'issue_type' => 'required|in:operational,compliance,financial,security',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'approver_ids' => 'nullable|array',
            'approver_ids.*' => 'exists:users,id',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $data = [];
        foreach ($this->fieldsByType[$request->issue_type] as $field) {
            $data[$field] = $request->input("data.$field");
        }

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('risk_attachments');
        }

        $flow = $request->approver_ids ?? [];
        $firstApproverId = $flow[0] ?? optional(\App\Models\User::role('manager')->where('department_id', $request->department_id)->first())->id;

        $report = RiskReport::create([
            'risk_id' => '',
            'issue_type' => $request->issue_type,
            'department_id' => $request->department_id,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->has('submit') ? 'submitted' : 'draft',
            'current_approver_id' => $request->has('submit') ? $firstApproverId : null,
            'data' => $data,
            'attachment_path' => $path,
            'workflow_definition' => $flow,
        ]);

        $report->update(['risk_id' => $report->generateRiskId()]);

        if ($request->has('submit') && $firstApproverId) {
            $report->approvals()->create([
                'step_order' => 0,
                'user_id' => $firstApproverId,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('risk.reports.show', $report)
            ->with('success', $request->has('submit') ? 'Risk submitted' : 'Risk saved as draft');
    }

    public function show(RiskReport $report)
    {
        return view('risk.reports.show', [
            'report' => $report,
            'fields' => $this->fieldsByType[$report->issue_type] ?? [],
        ]);
    }

    public function submit(Request $request, RiskReport $report)
    {
        $this->authorize('update', $report);
        $flow = $report->workflow_definition ?? [];
        $firstApproverId = $flow[0] ?? optional(\App\Models\User::role('manager')->where('department_id', $report->department_id)->first())->id;
        $report->update([
            'status' => 'submitted',
            'submitted_by' => auth()->id(),
            'submitted_at' => now(),
            'current_approver_id' => $firstApproverId,
        ]);
        if ($firstApproverId) {
            $report->approvals()->create([
                'step_order' => 0,
                'user_id' => $firstApproverId,
                'status' => 'pending',
            ]);
        }
        return back()->with('success', 'Risk submitted');
    }

    public function approve(Request $request, RiskReport $report)
    {
        $this->authorize('update', $report);
        abort_unless($report->current_approver_id == auth()->id(), 403);

        $approval = $report->approvals()->where('status','pending')->first();
        if ($approval) {
            $approval->status = 'approved';
            $approval->acted_at = now();
            $approval->comments = $request->comments;
            $approval->save();
        }

        $flow = $report->workflow_definition ?? [];
        $nextApproverId = null;
        if (!empty($flow)) {
            $idx = array_search(auth()->id(), $flow);
            if ($idx !== false && $idx < count($flow) - 1) {
                $nextApproverId = $flow[$idx + 1];
            }
        }

        if ($nextApproverId) {
            $report->update([
                'current_approver_id' => $nextApproverId,
                'status' => 'submitted',
            ]);
            $report->approvals()->create([
                'step_order' => ($approval->step_order ?? 0) + 1,
                'user_id' => $nextApproverId,
                'status' => 'pending',
            ]);
            return back()->with('success', 'Step approved; moved to next approver');
        }

        $report->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'current_approver_id' => null,
        ]);
        return back()->with('success', 'Risk approved');
    }

    public function reject(Request $request, RiskReport $report)
    {
        $this->authorize('update', $report);
        abort_unless($report->current_approver_id == auth()->id(), 403);
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $approval = $report->approvals()->where('status','pending')->first();
        if ($approval) {
            $approval->status = 'rejected';
            $approval->comments = $request->rejection_reason;
            $approval->acted_at = now();
            $approval->save();
        }

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'current_approver_id' => null,
        ]);
        return back()->with('success', 'Risk rejected');
    }

    public function edit(RiskReport $report)
    {
        $this->authorize('update', $report);
        return view('risk.reports.edit', [
            'report' => $report,
            'fields' => $this->fieldsByType[$report->issue_type] ?? [],
        ]);
    }

    public function update(Request $request, RiskReport $report)
    {
        $this->authorize('update', $report);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data = $report->data;
        foreach (($this->fieldsByType[$report->issue_type] ?? []) as $field) {
            $data[$field] = $request->input("data.$field");
        }
        $report->update([
            'title' => $request->title,
            'description' => $request->description,
            'data' => $data,
        ]);
        return redirect()->route('risk.reports.show', $report)->with('success', 'Risk updated');
    }

    public function destroy(RiskReport $report)
    {
        $this->authorize('delete', $report);
        if ($report->attachment_path) {
            Storage::delete($report->attachment_path);
        }
        $report->delete();
        return redirect()->route('risk.reports.index')->with('success', 'Risk deleted');
    }
}
