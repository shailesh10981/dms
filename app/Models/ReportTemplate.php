<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'code',
    'description',
    'department_id',
    'frequency',
    'due_date',
    'is_active'
  ];

  public function department()
  {
    return $this->belongsTo(Department::class);
  }

  public function fields()
  {
    return $this->hasMany(ReportTemplateField::class)->orderBy('order');
  }

  public function reports()
  {
    return $this->hasMany(ComplianceReport::class);
  }
}
