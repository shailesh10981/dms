<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ComplianceTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplianceTemplatePolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('template_manage');
    }

    public function view(User $user, ComplianceTemplate $complianceTemplate)
    {
        return $user->hasPermissionTo('template_manage') ||
            ($user->department_id === $complianceTemplate->department_id &&
                $user->hasPermissionTo('template_view'));
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('template_manage');
    }

    public function update(User $user, ComplianceTemplate $complianceTemplate)
    {
        return $user->hasPermissionTo('template_manage');
    }

    public function delete(User $user, ComplianceTemplate $complianceTemplate)
    {
        return $user->hasPermissionTo('template_manage');
    }
}
