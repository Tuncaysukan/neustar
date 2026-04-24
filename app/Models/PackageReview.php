<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageReview extends Model
{
    protected $fillable = [
        'internet_package_id',
        'name',
        'rating',
        'comment',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
    ];

    public function package()
    {
        return $this->belongsTo(InternetPackage::class, 'internet_package_id');
    }
}

