<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * İl ve ilçe bazlı tarife sayfaları için SEO içerik tablosu.
     *
     * page_key örnekleri:
     *   tariff_city:istanbul
     *   tariff_district:istanbul:pendik
     */
    public function up(): void
    {
        Schema::create('tariff_seo_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique()->comment('tariff_city:{il} veya tariff_district:{il}:{ilçe}');
            $table->string('city_slug');
            $table->string('city_name');
            $table->string('district_slug')->nullable();
            $table->string('district_name')->nullable();

            // H1 ve giriş metni
            $table->string('h1_title')->nullable();
            $table->text('intro_text')->nullable();

            // Sayfa altı SEO metni (statik bölge bilgisi)
            $table->text('seo_footer_text')->nullable();

            // Meta etiketleri
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_seo_contents');
    }
};
