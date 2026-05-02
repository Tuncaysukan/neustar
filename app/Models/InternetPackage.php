<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternetPackage extends Model
{
    protected $fillable = [
        'operator_id',
        'name',
        'slug',
        'price',
        'speed',
        'upload_speed',
        'quota',
        'commitment_period',
        'infrastructure_type',
        'features',
        'description',
        'advantages',
        'disadvantages',
        'is_active',
        'is_sponsored',
        'affiliate_url',
        'modem_included',
        'available_provinces',
        'seo_title',
        'seo_description',
        'apply_type',
        'external_url',
        'call_number',
        'click_count',
    ];

    protected $casts = [
        'features'            => 'array',
        'available_provinces' => 'array',
        'is_active'           => 'boolean',
        'is_sponsored'        => 'boolean',
        'price'               => 'decimal:2',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function reviews()
    {
        return $this->hasMany(PackageReview::class, 'internet_package_id');
    }

    public function clicks()
    {
        return $this->hasMany(\App\Models\PackageClick::class, 'internet_package_id');
    }
}
