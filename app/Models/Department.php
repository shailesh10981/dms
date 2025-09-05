<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function complianceReports()
    {
        return $this->hasMany(ComplianceReport::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
