<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Beneficiary extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id']; // Allow mass assignment for simplicity in this demo, but explicit fillable is better practice. Let's use fillable.

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'government_id',
        'contact_number',
        'email',
        'address',
        'latitude',
        'longitude',
        'assigned_project_id',
        'submitted_by',
        'status',
        'biometric_template',
        'biometric_device',
        'biometric_enrolled_at',
        'is_verified',
        'contact_number_2',
        'passport_number',
        'profile_image',
        'employment_status',
        'occupation',
        'beneficiary_type',
        'no_of_households',
        'urgent_need',
        'detail_situation',
        'monthly_income',
        'monthly_expenditure',
        'debts',
        'asset_value',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class , 'assigned_project_id');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class , 'beneficiary_package');
    }

    public function submitter()
    {
        return $this->belongsTo(User::class , 'submitted_by');
    }

    public function documents()
    {
        return $this->hasMany(BeneficiaryDocument::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function latestReview()
    {
        return $this->hasOne(Review::class)->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
