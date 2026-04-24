<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Meta şablon tablosu.
     *
     * Placeholder'lar: {il}, {ilce}, {il_seo}, {ilce_seo}
     *   {il}      → "İstanbul"
     *   {ilce}    → "Kadıköy"
     *   {il_seo}  → "istanbul"  (slug)
     *   {ilce_seo}→ "kadikoy"   (slug)
     *
     * type: 'city' | 'district'
     */
    public function up(): void
    {
        Schema::create('location_meta_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Şablon adı (admin için)');
            $table->enum('type', ['city', 'district'])->default('district');
            $table->string('meta_title_tpl')->nullable()
                ->comment('Örn: {il} {ilce} Ev İnterneti Altyapı Sorgulama ve Paketleri');
            $table->text('meta_description_tpl')->nullable();
            $table->string('h1_tpl')->nullable();
            $table->text('intro_tpl')->nullable();
            $table->text('seo_footer_tpl')->nullable();
            $table->boolean('is_default')->default(false)
                ->comment('Sayfaya özel kayıt yoksa bu şablon kullanılır');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_meta_templates');
    }
};
