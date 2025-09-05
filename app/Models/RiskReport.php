<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'risk_id',
        'issue_type',
        'department_id',
        'created_by',
        'submitted_by',
        'approved_by',
        'title',
        'description',
        'status',
        'data',
        'attachment_path',
        'submitted_at',
        'approved_at',
        'rejection_reason',
        'workflow_definition',
    ];

    protected $casts = [
        'data' => 'array',
        'submitted_at' => 'date',
        'approved_at' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approvals()
    {
        return $this->hasMany(RiskReportApproval::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(RiskAuditLog::class);
    }

    public function generateRiskId(): string
    {
        $dept = $this->department ? $this->department->code : 'GEN';
        $date = now()->format('Ymd');
        $seq = self::whereDate('created_at', today())->count() + 1;
        return 'RSK-' . $dept . '-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
