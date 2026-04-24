<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->string('affiliate_url', 500)->nullable()->after('is_sponsored');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn('affiliate_url');
        });
    }
};
