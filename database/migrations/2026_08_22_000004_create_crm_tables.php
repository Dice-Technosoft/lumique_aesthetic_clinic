<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->string('source')->default('Website Contact Form');
            $table->string('type')->default('contact')->index(); // contact, appointment, general
            $table->string('status')->default('new')->index(); // new, contacted, in_progress, converted, closed, spam
            $table->string('priority')->default('medium')->index(); // low, medium, high
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->nullable()->constrained('inquiries')->nullOnDelete();
            $table->foreignId('lead_source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('status')->default('new')->index(); // new, contacted, qualified, follow_up, converted, lost
            $table->string('priority')->default('medium')->index(); // low, medium, high, urgent
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('follow_up_date')->index();
            $table->time('follow_up_time')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('pending')->index(); // pending, completed, cancelled
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type');
            $table->text('description');
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_followups');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('lead_sources');
    }
};
