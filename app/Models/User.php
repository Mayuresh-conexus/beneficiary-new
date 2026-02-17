<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasPermissions;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ORGANIZATION_ADMIN = 'organization_admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_VOLUNTEER = 'volunteer';
    const ROLE_AUDITOR = 'auditor';
    const ROLE_FINANCIAL_OFFICER = 'financial_officer';

    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ORGANIZATION_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_VOLUNTEER,
            self::ROLE_AUDITOR,
            self::ROLE_FINANCIAL_OFFICER,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'organization_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class , 'project_user');
    }

    public function submittedBeneficiaries()
    {
        return $this->hasMany(Beneficiary::class , 'submitted_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at')->latest();
    }

    // Helpers

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }
}
