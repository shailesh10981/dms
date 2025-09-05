<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceReportApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'status',
        'comments',
        'acted_at'
    ];

    protected $casts = [
        'acted_at' => 'datetime',
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
