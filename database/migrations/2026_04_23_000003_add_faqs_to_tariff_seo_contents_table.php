<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariff_seo_contents', function (Blueprint $table) {
            // Sayfa bazlı SSS: [{"question":"...","answer":"..."},...]
            $table->json('faqs')->nullable()->after('seo_footer_text');
        });
    }

    public function down(): void
    {
        Schema::table('tariff_seo_contents', function (Blueprint $table) {
            $table->dropColumn('faqs');
        });
    }
};
