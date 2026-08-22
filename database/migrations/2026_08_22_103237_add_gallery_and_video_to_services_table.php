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
        Schema::table('services', function (Blueprint $table) {
            $table->json('gallery_images')->nullable()->after('featured_image');
            $table->string('video_url')->nullable()->after('gallery_images');
            $table->string('video_title')->nullable()->after('video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['gallery_images', 'video_url', 'video_title']);
        });
    }
};
