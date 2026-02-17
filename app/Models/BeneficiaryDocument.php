<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id',
        'type',
        'file_path',
        'uploaded_by',
    ];

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class , 'uploaded_by');
    }
}
