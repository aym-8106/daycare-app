<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'break_start',
        'break_end',
        'overtime_start',
        'overtime_end',
        'break_minutes',
        'overtime_minutes',
        'work_minutes',
        'status',
        'note',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'overtime_start' => 'datetime:H:i',
        'overtime_end' => 'datetime:H:i',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopeByMonth($query, string $yearMonth)
    {
        return $query->whereYear('work_date', substr($yearMonth, 0, 4))
                    ->whereMonth('work_date', substr($yearMonth, 4, 2));
    }

    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    public function calculateWorkMinutes(): int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $clockIn = \Carbon\Carbon::parse($this->clock_in);
        $clockOut = \Carbon\Carbon::parse($this->clock_out);

        $totalMinutes = $clockOut->diffInMinutes($clockIn);
        $workMinutes = $totalMinutes - $this->break_minutes;

        return max(0, $workMinutes);
    }

    public function calculateOvertimeMinutes(): int
    {
        if (!$this->overtime_start || !$this->overtime_end) {
            return 0;
        }

        $overtimeStart = \Carbon\Carbon::parse($this->overtime_start);
        $overtimeEnd = \Carbon\Carbon::parse($this->overtime_end);

        return $overtimeEnd->diffInMinutes($overtimeStart);
    }
}