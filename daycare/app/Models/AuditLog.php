<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'office_id',
        'user_id',
        'entity',
        'entity_id',
        'action',
        'payload',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
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

    public function scopeByEntity($query, string $entity, ?int $entityId = null)
    {
        $query = $query->where('entity', $entity);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        return $query;
    }

    public static function log(
        int $officeId,
        ?int $userId,
        string $entity,
        ?int $entityId,
        string $action,
        ?array $payload = null,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): self {
        return self::create([
            'office_id' => $officeId,
            'user_id' => $userId,
            'entity' => $entity,
            'entity_id' => $entityId,
            'action' => $action,
            'payload' => $payload,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}