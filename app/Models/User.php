<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'phone',
        'address',
        'profile_picture',
        'employee_id',
        'joining_date',
        'birth_date',
        'gender',
        'ldap_dn',
        'ldap_username',
        'department',
        'title',
        'is_ldap_user',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'joining_date' => 'date',
        'birth_date' => 'date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function approvedDocuments()
    {
        return $this->hasMany(Document::class, 'current_approver_id');
    }

    public function submittedReports()
    {
        return $this->hasMany(ComplianceReport::class, 'submitted_by');
    }

    public function approvedReports()
    {
        return $this->hasMany(ComplianceReport::class, 'approved_by');
    }

    /**
     * Check if user is authenticated via LDAP.
     */
    public function isLdapUser(): bool
    {
        return $this->is_ldap_user;
    }

    /**
     * Get the user's department name (from LDAP or database).
     */
    public function getDepartmentName(): ?string
    {
        if ($this->isLdapUser() && $this->department) {
            return $this->department;
        }

        return $this->department?->name;
    }

    /**
     * Check if user is HOD/Manager.
     */
    public function isHod(): bool
    {
        return $this->hasRole('Manager');
    }

    /**
     * Get user's full display name.
     */
    public function getDisplayName(): string
    {
        return $this->name ?: $this->ldap_username ?: $this->email;
    }
}
