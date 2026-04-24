<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfrastructureLead extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'email',
        'city_slug', 'district_slug',
        'city_name', 'district_name', 'neighborhood_name',
        'street', 'building_no',
        'lookup_snapshot',
        'status', 'admin_notes',
        'ip', 'user_agent',
        'kvkk_approved_at',
    ];

    protected $casts = [
        'lookup_snapshot' => 'array',
    ];

    /** Admin listesi için insan-dostu durum etiketleri. */
    public const STATUS_LABELS = [
        'new'       => 'Yeni',
        'contacted' => 'Arandı',
        'converted' => 'Satış',
        'rejected'  => 'Uygun değil',
        'spam'      => 'Spam',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /** Listedeki rozet rengi. */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'new'       => 'bg-blue-100 text-blue-800',
            'contacted' => 'bg-amber-100 text-amber-800',
            'converted' => 'bg-green-100 text-green-800',
            'rejected'  => 'bg-gray-100 text-gray-700',
            'spam'      => 'bg-red-100 text-red-800',
            default     => 'bg-gray-100 text-gray-700',
        };
    }
}
