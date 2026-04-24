<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infrastructure_leads', function (Blueprint $table) {
            $table->id();

            // Başvuru sahibi
            $table->string('full_name', 120);
            $table->string('phone', 32);
            $table->string('email', 160)->nullable();

            // Adres scope'u
            $table->string('city_slug', 96)->index();
            $table->string('district_slug', 96)->nullable()->index();
            $table->string('city_name', 96);
            $table->string('district_name', 96)->nullable();
            $table->string('neighborhood_name', 160)->nullable();
            $table->string('street', 200)->nullable();
            $table->string('building_no', 40)->nullable();

            // Servis tarafının o anki cevabı — JSON snapshot
            $table->json('lookup_snapshot')->nullable();

            // CRM durumu
            $table->enum('status', ['new', 'contacted', 'converted', 'rejected', 'spam'])
                ->default('new')
                ->index();
            $table->text('admin_notes')->nullable();

            // Teknik iz
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_leads');
    }
};
