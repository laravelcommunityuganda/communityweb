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
        // In-app notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type'); // Notification class name
            $table->morphs('notifiable'); // User who receives the notification
            $table->text('data'); // JSON data
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'notifiable_type', 'read_at']);
        });

        // Custom notifications for more control
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('type'); // post_reply, comment_mention, job_match, event_reminder, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data
            $table->morphs('related'); // Related entity (post, comment, job, event)
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_emailed')->default(false);
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('sender_id');
            $table->index('type');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notifications');
    }
};
