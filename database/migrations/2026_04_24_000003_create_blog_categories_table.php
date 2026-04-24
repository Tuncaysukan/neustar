<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('blog_category_id')->nullable()->after('id');
            $table->foreign('blog_category_id')->references('id')->on('blog_categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropForeign(['blog_category_id']);
            $table->dropColumn('blog_category_id');
        });
        Schema::dropIfExists('blog_categories');
    }
};
