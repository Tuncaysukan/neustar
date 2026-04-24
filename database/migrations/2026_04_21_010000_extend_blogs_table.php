<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (! Schema::hasColumn('blogs', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('content');
            }
            if (! Schema::hasColumn('blogs', 'category')) {
                $table->string('category')->nullable()->after('excerpt');
            }
            if (! Schema::hasColumn('blogs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('blogs', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('published_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            foreach (['excerpt', 'category', 'published_at', 'views'] as $col) {
                if (Schema::hasColumn('blogs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
