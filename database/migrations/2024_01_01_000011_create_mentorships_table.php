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
        // Mentor profiles
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio');
            $table->json('expertise_areas'); // Areas of expertise
            $table->json('skills'); // Skills they can mentor on
            $table->integer('years_of_experience');
            $table->string('current_role');
            $table->string('company');
            $table->integer('max_mentees')->default(5); // Max mentees at a time
            $table->integer('current_mentees_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('sessions_count')->default(0);
            $table->boolean('is_available')->default(true);
            $table->json('availability')->nullable(); // Available times
            $table->boolean('is_verified')->default(false); // Admin verified
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_available');
            $table->index('rating_average');
        });

        // Mentorship requests
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->text('goals'); // What mentee wants to achieve
            $table->text('message')->nullable(); // Initial message
            $table->enum('status', ['pending', 'accepted', 'rejected', 'active', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('sessions_count')->default(0);
            $table->text('mentor_notes')->nullable();
            $table->text('mentee_notes')->nullable();
            $table->timestamps();

            $table->index('mentor_id');
            $table->index('mentee_id');
            $table->index('status');
        });

        // Mentorship sessions
        Schema::create('mentorship_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('agenda')->nullable();
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->string('meeting_url')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('mentorship_id');
            $table->index('scheduled_at');
            $table->index('status');
        });

        // Mentorship ratings
        Schema::create('mentorship_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->onDelete('cascade');
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mentee_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique('mentorship_id');
            $table->index('mentor_id');
            $table->index('mentee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_ratings');
        Schema::dropIfExists('mentorship_sessions');
        Schema::dropIfExists('mentorships');
        Schema::dropIfExists('mentor_profiles');
    }
};
