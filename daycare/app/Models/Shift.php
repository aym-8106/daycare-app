<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'user_id',
        'year_month',
        'day',
        'shift_code',
        'start_time',
        'end_time',
        'note',
        'is_confirmed',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_confirmed' => 'boolean',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopeByMonth($query, string $yearMonth)
    {
        return $query->where('year_month', $yearMonth);
    }

    public function getWorkDateAttribute(): \Carbon\Carbon
    {
        return \Carbon\Carbon::createFromFormat('Ym', $this->year_month)->day($this->day);
    }

    public function isRestDay(): bool
    {
        return in_array($this->shift_code, ['休', 'OFF', '有休']);
    }
}