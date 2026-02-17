<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'type',
        'value',
        'frequency',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class , 'project_package');
    }

    public function beneficiaries()
    {
        return $this->belongsToMany(Beneficiary::class , 'beneficiary_package');
    }
}
