<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'program_id',
        'name',
        'description',
        'location',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class , 'project_package');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class , 'assigned_project_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class , 'project_user');
    }
}
