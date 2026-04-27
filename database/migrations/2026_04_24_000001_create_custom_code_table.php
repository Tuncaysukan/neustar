<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admin'den düzenlenebilen CSS/JS snippet'leri.
     * key: 'custom_css' | 'custom_js' | 'variables_css'
     */
    public function up(): void
    {
        Schema::create('custom_codes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_codes');
    }
};
