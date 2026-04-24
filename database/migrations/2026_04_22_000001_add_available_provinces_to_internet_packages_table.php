<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            // null = tüm Türkiye'de geçerli, dolu ise sadece belirtilen il slug'larında göster
            $table->json('available_provinces')->nullable()->after('is_sponsored')
                ->comment('null = tüm Türkiye, dolu ise sadece bu il slug\'larında göster');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn('available_provinces');
        });
    }
};
