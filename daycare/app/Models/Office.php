<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'business_days',
        'open_time',
        'close_time',
        'standard_work_hours',
        'standard_break_minutes',
        'holidays',
        'is_active',
    ];

    protected $casts = [
        'business_days' => 'array',
        'holidays' => 'array',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function dailySchedules(): HasMany
    {
        return $this->hasMany(DailySchedule::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isBusinessDay(int $dayOfWeek): bool
    {
        return in_array($dayOfWeek, $this->business_days ?? []);
    }
}