<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'beneficiary_id',
        'project_id',
        'package_id',
        'financial_officer_id',
        'delivered_by',
        'amount',
        'currency',
        'status',
        'delivery_date',
        'signature_path',
        'document_path',
        'remarks',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string)Str::uuid();
        });
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function financialOfficer()
    {
        return $this->belongsTo(User::class , 'financial_officer_id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class , 'delivered_by');
    }
}
