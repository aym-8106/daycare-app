<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'office_id',
        'employee_id',
        'name',
        'email',
        'password',
        'employment_type',
        'position',
        'available_days',
        'default_break_minutes',
        'paid_leave_days',
        'hire_date',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'available_days' => 'array',
        'hire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
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
        return $this->hasMany(DailySchedule::class, 'staff_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function messageReads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin_office');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    public function canWorkOnDay(int $dayOfWeek): bool
    {
        return in_array($dayOfWeek, $this->available_days ?? []);
    }
}