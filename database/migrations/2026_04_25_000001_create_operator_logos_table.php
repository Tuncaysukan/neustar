<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_logos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('cascade');
            $table->string('path');                          // storage path veya URL
            $table->string('label')->default('');            // "Açık tema", "Koyu tema", "Favicon" vb.
            $table->enum('variant', ['light', 'dark', 'favicon', 'other'])->default('light');
            $table->boolean('is_primary')->default(false);   // Ana logo
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_logos');
    }
};
