<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'manager_id',
        'action',
        'remarks',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class , 'manager_id');
    }
}
