<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskReportApproval extends Model
{
    protected $fillable = [
        'risk_report_id',
        'step_order',
        'user_id',
        'status',
        'comments',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
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
