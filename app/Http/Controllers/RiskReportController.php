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

        $report = RiskReport::create([
            'risk_id' => '',
            'issue_type' => $request->issue_type,
            'department_id' => $request->department_id,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->has('submit') ? 'submitted' : 'draft',
            'data' => $data,
            'attachment_path' => $path,
            'workflow_definition' => $request->approver_ids,
        ]);

        $report->update(['risk_id' => $report->generateRiskId()]);

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
