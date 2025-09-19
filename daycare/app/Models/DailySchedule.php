<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'work_date',
        'time_slot',
        'staff_id',
        'client_name',
        'activity',
        'color',
        'memo',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function scopeByOffice($query, int $officeId)
    {
        return $query->where('office_id', $officeId);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->where('work_date', $date);
    }

    public function getActivityColorAttribute(): string
    {
        $colors = [
            'transport' => '#FF6B6B',    // 送迎 - 赤
            'bath' => '#4ECDC4',         // 入浴 - 青緑
            'rehab' => '#45B7D1',        // 機能訓練 - 青
            'meal' => '#FFA726',         // 食事 - オレンジ
            'recreation' => '#AB47BC',   // レクリエーション - 紫
            'other' => '#78909C',        // その他 - グレー
        ];

        return $this->color ?: ($colors[$this->activity] ?? '#E0E0E0');
    }

    public function getActivityNameAttribute(): string
    {
        $names = [
            'transport' => '送迎',
            'bath' => '入浴',
            'rehab' => '機能訓練',
            'meal' => '食事',
            'recreation' => 'レクリエーション',
            'other' => 'その他',
        ];

        return $names[$this->activity] ?? $this->activity;
    }
}