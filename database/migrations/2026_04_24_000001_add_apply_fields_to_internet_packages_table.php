<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('internet_packages', function (Blueprint $blueprint) {
            $blueprint->string('apply_type')->default('form')->comment('site, call, form');
            $blueprint->string('external_url')->nullable();
            $blueprint->string('call_number')->nullable();
        });
    }

    public function down()
    {
        Schema::table('internet_packages', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['apply_type', 'external_url', 'call_number']);
        });
    }
};
