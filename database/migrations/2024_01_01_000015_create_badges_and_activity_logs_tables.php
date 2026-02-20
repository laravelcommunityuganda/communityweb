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
        // Badges
        if (!Schema::hasTable('badges')) {
            Schema::create('badges', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description');
                $table->string('icon'); // Icon class or image path
                $table->string('color', 7)->default('#3B82F6');
                $table->enum('type', ['achievement', 'skill', 'contribution', 'special']);
                $table->integer('points')->default(0);
                $table->json('criteria'); // Requirements to earn badge
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('slug');
                $table->index('type');
                $table->index('is_active');
            });
        }

        // User badges
        if (!Schema::hasTable('user_badges')) {
            Schema::create('user_badges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('badge_id')->constrained()->onDelete('cascade');
                $table->timestamp('earned_at');
                $table->timestamps();

                $table->unique(['user_id', 'badge_id']);
                $table->index('user_id');
                $table->index('badge_id');
            });
        }

        // Activity logs
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->string('type'); // login, post_created, comment_added, etc.
                $table->string('action');
                $table->nullableMorphs('subject'); // Related entity
                $table->json('properties')->nullable(); // Additional data
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('type');
                $table->index('action');
                $table->index('created_at');
            });
        }

        // Reputation history
        if (!Schema::hasTable('reputation_history')) {
            Schema::create('reputation_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->integer('points');
                $table->string('action'); // post_upvoted, answer_accepted, etc.
                $table->nullableMorphs('source'); // What caused the reputation change
                $table->timestamps();

                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reputation_history');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
