<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'subject_type',
        'subject_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    // Scopes

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Create a notification for a specific user.
     */
    public static function send(int $userId, string $type, string $title, string $message, array $extra = []): self
    {
        $iconMap = [
            'beneficiary_submitted' => ['icon' => 'person_add', 'color' => 'sky'],
            'beneficiary_approved' => ['icon' => 'check_circle', 'color' => 'emerald'],
            'beneficiary_rejected' => ['icon' => 'cancel', 'color' => 'red'],
            'fraud_flagged' => ['icon' => 'warning', 'color' => 'rose'],
            'project_assigned' => ['icon' => 'assignment_ind', 'color' => 'violet'],
            'review_needed' => ['icon' => 'rate_review', 'color' => 'amber'],
            'system' => ['icon' => 'info', 'color' => 'primary'],
        ];

        $defaults = $iconMap[$type] ?? $iconMap['system'];

        return self::create(array_merge([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $defaults['icon'],
            'color' => $defaults['color'],
        ], $extra));
    }

    /**
     * Send a notification to multiple users.
     */
    public static function sendToMany(array $userIds, string $type, string $title, string $message, array $extra = []): void
    {
        foreach ($userIds as $userId) {
            self::send($userId, $type, $title, $message, $extra);
        }
    }
}
