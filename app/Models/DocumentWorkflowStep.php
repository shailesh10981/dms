<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentWorkflowStep extends Model
{
    protected $fillable = [
        'document_id',
        'step_order',
        'approver_id',
        'status',
        'comments',
        'acted_at'
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
