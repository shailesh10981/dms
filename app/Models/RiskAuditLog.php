<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskAuditLog extends Model
{
    protected $fillable = [
        'risk_report_id',
        'user_id',
        'action',
        'comments',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(RiskReport::class, 'risk_report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
