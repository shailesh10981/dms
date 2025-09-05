<?php

namespace App\Policies;

use App\Models\User;

class DocumentAuditLogPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('audit_logs_view') || $user->hasRole('admin');
    }
}
