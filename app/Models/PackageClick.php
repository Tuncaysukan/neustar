<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageClick extends Model
{
    protected $fillable = ['internet_package_id', 'ip', 'user_agent', 'referer'];

    public function package()
    {
        return $this->belongsTo(InternetPackage::class, 'internet_package_id');
    }
}
