<?php

namespace App\Http\Controllers;

use App\Models\ComplianceReport;
use App\Models\ComplianceTemplate;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReportSubmittedNotification;
use App\Notifications\ReportApprovedNotification;
use App\Notifications\ReportRejectedNotification;
use App\Services\ComplianceAuditService;

class ComplianceReportController extends Controller
{
  public function index(Request $request)
  {
    $reports = ComplianceReport::query()
      ->with(['template', 'department', 'creator'])
      ->when($request->status, function ($query, $status) {
        return $query->where('status', $status);
      })
      ->when($request->template_id, function ($query, $templateId) {
        return $query->where('template_id', $templateId);
      })
      ->when($request->department_id, function ($query, $departmentId) {
        return $query->where('department_id', $departmentId);
      })
      ->latest()
      ->paginate(20);

    $templates = ComplianceTemplate::pluck('name', 'id');
    $departments = Department::pluck('name', 'id');

    return view('compliance.reports.index', compact('reports', 'templates', 'departments'));
  }

  public function create()
  {
    $templates = ComplianceTemplate::where('is_active', true)
      ->when(!auth()->user()->hasRole('admin'), function ($query) {
        $query->where('department_id', auth()->user()->department_id);
      })
      ->get();

    return view('compliance.reports.select-template', compact('templates'));
  }

  public function createWithTemplate(ComplianceTemplate $template)
  {
    $this->authorize('create', ComplianceReport::class);

    if (!$template->exists) {
      abort(404, 'Template not found');
    }

    return view('compliance.reports.create', [
      'template' => $template,
      'fields' => $template->fields()->orderBy('order')->get()
    ]);
  }

  public function createFromTemplate(ComplianceTemplate $template)
  {
    $this->authorize('create', ComplianceReport::class);

    // Verify template exists and is active
    if (!$template->exists || !$template->is_active) {
      abort(404, 'Template not found or inactive');
    }

    return view('compliance.reports.create', [
      'template' => $template,
      'fields' => $template->fields()->orderBy('order')->get()
    ]);
  }

  public function store(Request $request, ComplianceTemplate $template)
  {
    $this->authorize('create', ComplianceReport::class);

    // Verify template exists
    if (!$template->exists) {
      abort(404, 'Template not found');
    }

    $validationRules = $this->buildValidationRules($template);
    $validatedData = $request->validate($validationRules);

    // Create report with all required fields
    $reportData = [
      'template_id' => $template->id,
      'department_id' => $template->department_id ?? auth()->user()->department_id,
      'created_by' => auth()->id(),
      'title' => $template->name . ' - ' . now()->format('Y-m-d'),
      'due_date' => now()->addDays(7),
      'description' => $validatedData['description'] ?? null,
      'data' => $validatedData['fields'] ?? [],
      'report_id' => '' // Temporary empty value
    ];

    $report = ComplianceReport::create($reportData);

    // Generate and update the report ID
    $report->update([
      'report_id' => $report->generateReportId()
    ]);

    // Log the creation after the report exists
    ComplianceAuditService::log(
      $report,
      'created',
      'Report created',
      ['data' => $validatedData]
    );

    if ($request->has('submit')) {
      $report->update([
        'status' => 'submitted',
        'submitted_by' => auth()->id(),
        'submitted_at' => now()
      ]);

      // Log the submission
      ComplianceAuditService::log(
        $report,
        'submitted',
        $request->input('submission_notes', 'Report submitted')
      );

      $this->notifyApprovers($report);
    }

    return redirect()->route('compliance.reports.show', $report)
      ->with('success', $request->has('submit')
        ? 'Report submitted successfully!'
        : 'Report saved as draft.');
  }

  public function show(ComplianceReport $report)
  {
    $this->authorize('view', $report);

    return view('compliance.reports.show', [
      'report' => $report,
      'template' => $report->template,
      'fields' => $report->template->fields()->orderBy('order')->get(),
      'approvals' => $report->approvals()->with('user')->get()
    ]);
  }

  public function edit(ComplianceReport $report)
  {
    $this->authorize('update', $report);

    return view('compliance.reports.edit', [
      'report' => $report,
      'template' => $report->template,
      'fields' => $report->template->fields()->orderBy('order')->get()
    ]);
  }

  public function update(Request $request, ComplianceReport $report)
  {
    $this->authorize('update', $report);

    $validationRules = $this->buildValidationRules($report->template);
    $validatedData = $request->validate($validationRules);

    $report->data = $validatedData['fields'];
    $report->description = $request->description;

    if ($request->has('submit')) {
      $report->status = 'submitted';
      $report->submitted_by = auth()->id();
      $report->submitted_at = now();
    }

    $report->save();

    if ($request->has('submit')) {
      $this->notifyApprovers($report);
    }

    return redirect()->route('compliance.reports.show', $report)
      ->with('success', $request->has('submit')
        ? 'Report submitted successfully!'
        : 'Report updated successfully.');
  }

  public function destroy(ComplianceReport $report)
  {
    $this->authorize('delete', $report);

    $report->delete();

    return redirect()->route('compliance.reports.index')
      ->with('success', 'Report deleted successfully.');
  }

  public function submit(Request $request, ComplianceReport $report)
  {
    $this->authorize('submit', $report);

    // Validate submission notes if provided
    $validated = $request->validate([
      'submission_notes' => 'nullable|string|max:500'
    ]);

    // Update report status
    $report->update([
      'status' => 'submitted',
      'submitted_by' => auth()->id(),
      'submitted_at' => now()
    ]);

    // Log the submission
    ComplianceAuditService::log(
      $report,
      'submitted',
      $validated['submission_notes'] ?? 'Report submitted without notes'
    );

    // Notify approvers
    $this->notifyApprovers($report);

    return back()->with('success', 'Report submitted for approval!');
  }
  public function approve(Request $request, ComplianceReport $report)
  {
    $this->authorize('approve', $report);

    $report->approvals()->create([
      'user_id' => auth()->id(),
      'status' => 'approved',
      'comments' => $request->comments,
      'acted_at' => now()
    ]);

    $report->update([
      'status' => 'approved',
      'approved_by' => auth()->id(),
      'approved_at' => now()
    ]);

    $report->creator->notify(new ReportApprovedNotification($report));

    return back()->with('success', 'Report approved successfully!');
  }

  public function reject(Request $request, ComplianceReport $report)
  {
    $this->authorize('approve', $report);

    $request->validate([
      'rejection_reason' => 'required|string|max:500'
    ]);

    $report->approvals()->create([
      'user_id' => auth()->id(),
      'status' => 'rejected',
      'comments' => $request->rejection_reason,
      'acted_at' => now()
    ]);

    $report->update([
      'status' => 'rejected',
      'rejection_reason' => $request->rejection_reason
    ]);

    $report->creator->notify(new ReportRejectedNotification($report, $request->rejection_reason));

    return back()->with('success', 'Report rejected successfully!');
  }

  protected function buildValidationRules(ComplianceTemplate $template)
  {
    $rules = [
      'description' => 'nullable|string',
      'fields' => 'required|array',
    ];

    foreach ($template->fields as $field) {
      $fieldRules = [];

      if ($field->is_required) {
        $fieldRules[] = 'required';
      }

      // Add type-specific validation
      switch ($field->field_type) {
        case 'number':
          $fieldRules[] = 'numeric';
          break;
        case 'date':
          $fieldRules[] = 'date';
          break;
        case 'email':
          $fieldRules[] = 'email';
          break;
      }

      // Add custom validation rules
      if ($field->validation_rules) {
        $fieldRules = array_merge($fieldRules, $field->validation_rules);
      }

      $rules["fields.{$field->label}"] = $fieldRules;
    }

    return $rules;
  }

  protected function notifyApprovers(ComplianceReport $report)
  {
    $approvers = User::role('manager')
      ->where('department_id', $report->department_id)
      ->get();

    Notification::send($approvers, new ReportSubmittedNotification($report));
  }
}
