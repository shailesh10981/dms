<?php

namespace App\Policies;

use App\Models\RiskReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user, RiskReport $report): bool
    {
        return $user->hasRole('admin') ||
            $user->id === $report->created_by ||
            $user->id === $report->current_approver_id ||
            ($user->department_id === $report->department_id && $user->can('report_view'));
    }

    public function create(User $user): bool
    {
        return $user->can('report_create');
    }

    public function update(User $user, RiskReport $report): bool
    {
        if ($user->hasRole('admin')) return true;

        // Creator can edit draft
        if ($report->status === 'draft' && $report->created_by === $user->id && $user->can('report_edit')) {
            return true;
        }

        // Current approver can act on submitted items
        if ($report->status === 'submitted' && $report->current_approver_id === $user->id && $user->can('report_approve')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, RiskReport $report): bool
    {
        return $user->hasRole('admin') || ($report->status === 'draft' && $report->created_by === $user->id);
    }
}
