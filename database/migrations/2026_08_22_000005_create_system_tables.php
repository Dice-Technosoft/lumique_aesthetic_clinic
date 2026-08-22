<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_name');
            $table->string('path');
            $table->string('url')->nullable();
            $table->string('disk')->default('public');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->string('folder')->default('uploads');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->nullable()->index();
            $table->unsignedBigInteger('model_id')->nullable()->index();
            $table->string('path')->nullable()->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('robots')->default('index, follow');
            $table->json('schema_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->index();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, json, image, number
            $table->string('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('key');
            $table->json('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'key']);
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module')->index();
            $table->string('action')->index();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('seo_metas');
        Schema::dropIfExists('media');
    }
};
