<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceTemplateField extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'label',
        'field_type',
        'options',
        'is_required',
        'order',
        'validation_rules'
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'is_required' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(ComplianceTemplate::class);
    }
}
