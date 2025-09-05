<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplianceTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'department_id',
        'frequency',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function fields()
    {
        return $this->hasMany(ComplianceTemplateField::class, 'template_id')
            ->orderBy('order');
    }

    public function reports()
    {
        return $this->hasMany(ComplianceReport::class);
    }
}
