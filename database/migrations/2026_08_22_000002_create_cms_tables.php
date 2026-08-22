<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('template')->default('default');
            $table->string('status')->default('published')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('og_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('section_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->json('default_settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('section_type_id')->nullable()->constrained('section_types')->nullOnDelete();
            $table->string('section_type_key')->index();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->unique(); // header, footer, etc.
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->cascadeOnDelete();
            $table->string('title');
            $table->string('url');
            $table->string('route_name')->nullable();
            $table->string('target')->default('_self');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('secondary_button_text')->nullable();
            $table->string('secondary_button_url')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('section_types');
        Schema::dropIfExists('pages');
    }
};
