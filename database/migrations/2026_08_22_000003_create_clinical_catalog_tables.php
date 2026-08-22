<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->default('skin')->index(); // skin, hair, laser, body, tattoo-removal
            $table->string('icon')->nullable();
            $table->string('featured_image')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('price_starting_at')->nullable();
            $table->json('benefits')->nullable();
            $table->json('faqs')->nullable();
            $table->json('procedure_steps')->nullable();
            $table->string('status')->default('published')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('designation');
            $table->string('qualification');
            $table->string('department')->nullable();
            $table->text('short_bio')->nullable();
            $table->longText('full_bio')->nullable();
            $table->string('photo')->nullable();
            $table->integer('experience_years')->default(0);
            $table->json('social_links')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->boolean('is_lead')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('youtube_url');
            $table->string('youtube_video_id')->index();
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->default('all')->index();
            $table->string('status')->default('published')->index();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable()->index();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_id')->nullable()->constrained('galleries')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('image_before')->nullable();
            $table->string('image_after')->nullable();
            $table->string('image')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('category')->nullable()->index();
            $table->string('treatment_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('treatment_taken')->nullable();
            $table->string('photo')->nullable();
            $table->integer('rating')->default(5);
            $table->text('content');
            $table->string('source')->default('Google Review');
            $table->boolean('status')->default(true)->index();
            $table->boolean('is_featured')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('faq_categories')->nullOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->text('question');
            $table->longText('answer');
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->string('status')->default('published')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->integer('read_time_minutes')->default(5);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['blog_post_id', 'blog_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_tags');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
