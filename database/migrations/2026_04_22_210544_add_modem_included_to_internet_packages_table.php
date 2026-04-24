<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->string('modem_included', 20)->nullable()->after('affiliate_url')
                  ->comment('free, paid, or null');
        });
    }

    public function down(): void
    {
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn('modem_included');
        });
    }
};
