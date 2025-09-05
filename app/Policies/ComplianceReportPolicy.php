<?php

namespace App\Policies;

use App\Models\ComplianceReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplianceReportPolicy
{
  public function viewAny(User $user)
  {
    return $user->can('report_view');
  }

  public function view(User $user, ComplianceReport $report)
  {
    return $user->can('report_view') &&
      ($user->hasRole('admin') || $user->department_id === $report->department_id);
  }

  public function create(User $user)
  {
    return $user->can('report_create');
  }

  public function update(User $user, ComplianceReport $report)
  {
    return $user->can('report_edit') &&
      ($report->status === 'draft' || $user->hasRole('admin'));
  }

  public function delete(User $user, ComplianceReport $report)
  {
    return $user->can('report_delete') &&
      ($report->status === 'draft' || $user->hasRole('admin'));
  }

  public function submit(User $user, ComplianceReport $report)
  {
    return $user->can('report_submit') &&
      $report->status === 'draft' &&
      ($user->id === $report->created_by || $user->hasRole('admin'));
  }

  public function approve(User $user, ComplianceReport $report)
  {
    return $user->can('report_approve') &&
      $report->status === 'submitted' &&
      ($user->hasRole('manager') || $user->hasRole('admin'));
  }
}
