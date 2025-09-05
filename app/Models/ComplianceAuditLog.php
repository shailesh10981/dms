<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceAuditLog extends Model
{
    protected $fillable = [
        'report_id',
        'user_id',
        'action',
        'comments',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function report()
    {
        return $this->belongsTo(ComplianceReport::class, 'report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
