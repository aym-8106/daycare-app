<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'user_id',
        'title',
        'body',
        'is_pinned',
        'is_important',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_important' => 'boolean',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messageReads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function isReadBy(User $user): bool
    {
        return $this->messageReads()->where('user_id', $user->id)->exists();
    }

    public function markAsReadBy(User $user): void
    {
        $this->messageReads()->updateOrCreate(
            ['user_id' => $user->id],
            ['read_at' => now()]
        );
    }

    public function getReadCountAttribute(): int
    {
        return $this->messageReads()->count();
    }
}