<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplateField extends Model
{
  use HasFactory;

  protected $fillable = [
    'report_template_id',
    'field_name',
    'field_label',
    'field_type',
    'options',
    'is_required',
    'order',
    'validation_rules'
  ];

  protected $casts = [
    'options' => 'array',
    'is_required' => 'boolean'
  ];

  public function template()
  {
    return $this->belongsTo(ReportTemplate::class);
  }
}
