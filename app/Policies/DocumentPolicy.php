<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Document;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Document $document)
    {
        return $user->hasRole('admin') ||
            ($user->department_id == $document->department_id &&
                $user->hasPermissionTo('document_view'));
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('document_upload');
    }

    public function update(User $user, Document $document)
    {
        return $document->status === 'draft' &&
            $document->uploaded_by === $user->id &&
            $user->hasPermissionTo('document_edit') &&
            $user->department_id === $document->department_id;
    }

    public function delete(User $user, Document $document)
    {
        return $document->status === 'draft' &&
            ($document->uploaded_by === $user->id || $user->hasRole('admin')) &&
            $user->hasPermissionTo('document_delete');
    }

    public function submitForApproval(User $user, Document $document)
    {
        return $document->status === 'draft' &&
            $document->uploaded_by === $user->id &&
            $user->hasPermissionTo('document_edit') &&
            $user->department_id === $document->department_id;
    }

    public function approve(User $user, Document $document)
    {


        return $user->hasPermissionTo('document_approve') &&
            in_array($document->status, ['submitted', 'resubmitted']) &&
            $document->department_id === $user->department_id &&
            $document->current_approver_id === $user->id;
    }

    public function reject(User $user, Document $document)
    {
        Log::info('Reject Policy Check', [
            'user_id' => $user->id,
            'user_has_permission' => $user->hasPermissionTo('document_approve'),
            'doc_status' => $document->status,
            'current_approver_id' => $document->current_approver_id,
            'matches_approver' => $document->current_approver_id == $user->id,
        ]);
        //return $this->approve($user, $document);
        return in_array($document->status, ['submitted', 'resubmitted']) &&
            $document->current_approver_id === $user->id &&
            $user->hasPermissionTo('document_approve');
    }
    public function download(User $user, Document $document)
    {
        return $this->view($user, $document) &&
            $user->hasPermissionTo('document_download');
    }

    public function restore(User $user, Document $document)
    {
        return $user->hasRole('admin') ||
            ($document->uploaded_by === $user->id && $user->hasPermissionTo('document_restore'));
    }

    public function viewTrash(User $user)
    {
        return $user->hasPermissionTo('document_view_deleted') || $user->hasRole('admin');
    }
}
