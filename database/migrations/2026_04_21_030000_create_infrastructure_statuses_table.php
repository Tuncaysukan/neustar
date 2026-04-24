<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructure_statuses', function (Blueprint $table) {
            $table->id();

            // Slug'lar Str::slug() çıktılarıyla birebir uyumlu.
            $table->string('city_slug', 96)->index();
            $table->string('district_slug', 96)->nullable()->index();
            $table->string('neighborhood_slug', 128)->nullable()->index();

            // Görsel amaçlı okunabilir isimler (admin ekranı için).
            $table->string('city_name', 96)->nullable();
            $table->string('district_name', 96)->nullable();
            $table->string('neighborhood_name', 128)->nullable();

            // 0-100 kapsama oranları; null = bu seviyede bilinmiyor demek.
            $table->unsignedTinyInteger('fiber_coverage')->nullable();
            $table->unsignedTinyInteger('vdsl_coverage')->nullable();
            $table->unsignedTinyInteger('adsl_coverage')->nullable();

            // Maksimum hız (Mbps) — UI'da paketleri filtrelemek için.
            $table->unsignedSmallInteger('max_down_mbps')->nullable();
            $table->unsignedSmallInteger('max_up_mbps')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['city_slug', 'district_slug', 'neighborhood_slug'],
                'infra_scope_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_statuses');
    }
};
