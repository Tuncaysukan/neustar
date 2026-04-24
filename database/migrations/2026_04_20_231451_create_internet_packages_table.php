<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internet_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('speed')->comment('Mbps');
            $table->integer('upload_speed')->nullable()->comment('Mbps');
            $table->string('quota')->default('Sınırsız');
            $table->integer('commitment_period')->default(0)->comment('Month');
            $table->string('infrastructure_type')->nullable(); // Fiber, VDSL, ADSL
            $table->json('features')->nullable();
            $table->text('description')->nullable();
            $table->text('advantages')->nullable();
            $table->text('disadvantages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sponsored')->default(false);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internet_packages');
    }
};
