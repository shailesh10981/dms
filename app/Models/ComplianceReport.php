<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_id',
        'template_id', // Ensure this is included
        'department_id',
        'created_by',
        'submitted_by',
        'approved_by',
        'title',
        'description',
        'status',
        'due_date',
        'submitted_at',
        'approved_at',
        'rejection_reason',
        'data'
    ];

    protected $casts = [
        'data' => 'array',
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->template_id)) {
                throw new \Exception('Template ID is required');
            }
        });
    }

    public function template()
    {
        return $this->belongsTo(ComplianceTemplate::class);
    }

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
        return $this->hasMany(ComplianceReportApproval::class, 'report_id');
    }


    public function auditLogs()
    {
        return $this->hasMany(ComplianceAuditLog::class, 'report_id');
        // If you need to specify the local key:
        // return $this->hasMany(ComplianceAuditLog::class, 'report_id', 'id');
    }

    public function generateReportId()
    {
        // Ensure department relationship is loaded
        if (!$this->relationLoaded('department')) {
            $this->load('department');
        }

        // Fallback department code if not set
        $departmentCode = $this->department ? $this->department->code : 'GEN';
        $dateCode = now()->format('Ymd');
        $sequence = ComplianceReport::whereDate('created_at', today())->count() + 1;

        return "RPT-{$departmentCode}-{$dateCode}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
    public function getFieldValue($fieldName)
    {
        return $this->data[$fieldName] ?? null;
    }

    public function canBeEditedBy(User $user)
    {
        return $this->status === 'draft' &&
            ($user->id === $this->created_by || $user->hasRole('admin'));
    }

    public function canBeSubmittedBy(User $user)
    {
        return $this->status === 'draft' &&
            ($user->id === $this->created_by || $user->hasRole('admin'));
    }

    public function canBeApprovedBy(User $user)
    {
        return $this->status === 'submitted' &&
            ($user->hasRole('manager') || $user->hasRole('admin'));
    }
}
