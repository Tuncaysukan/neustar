<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('infrastructure_leads', function (Blueprint $table) {
            $table->timestamp('kvkk_approved_at')->nullable();
        });

        Schema::table('commitment_reminders', function (Blueprint $table) {
            $table->timestamp('kvkk_approved_at')->nullable();
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('ip')->nullable();
            $table->timestamp('kvkk_approved_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('infrastructure_leads', function (Blueprint $table) {
            $table->dropColumn('kvkk_approved_at');
        });

        Schema::table('commitment_reminders', function (Blueprint $table) {
            $table->dropColumn('kvkk_approved_at');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['ip', 'kvkk_approved_at']);
        });
    }
};
