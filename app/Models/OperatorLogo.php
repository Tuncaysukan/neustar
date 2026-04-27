<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OperatorLogo extends Model
{
    protected $fillable = ['operator_id', 'path', 'label', 'variant', 'is_primary', 'order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }
        return Storage::disk('public')->url($this->path);
    }
}
