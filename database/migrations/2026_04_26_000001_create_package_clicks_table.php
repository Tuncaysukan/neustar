<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internet_package_id')->constrained()->onDelete('cascade');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('referer', 500)->nullable();
            $table->timestamps();
        });

        // Paket tablosuna toplam tıklama sayacı ekle (hızlı okuma için)
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('click_count')->default(0)->after('is_sponsored');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn('click_count');
        });
        Schema::dropIfExists('package_clicks');
    }
};
