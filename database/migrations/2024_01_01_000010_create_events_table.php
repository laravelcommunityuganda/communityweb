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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Organizer
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description'); // Markdown content
            $table->string('featured_image')->nullable();
            $table->enum('type', ['meetup', 'workshop', 'conference', 'hackathon', 'webinar', 'other']);
            $table->enum('format', ['online', 'physical', 'hybrid'])->default('physical');
            $table->string('venue_name')->nullable();
            $table->string('venue_address')->nullable();
            $table->string('venue_city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('online_url')->nullable(); // For online events
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('timezone')->default('Africa/Nairobi');
            $table->integer('capacity')->nullable(); // Max attendees
            $table->integer('attendees_count')->default(0);
            $table->boolean('is_free')->default(true);
            $table->decimal('ticket_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('UGX');
            $table->string('ticket_url')->nullable(); // External ticketing
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->json('speakers')->nullable(); // Array of speaker info
            $table->json('sponsors')->nullable(); // Array of sponsor info
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('type');
            $table->index('format');
            $table->index('status');
            $table->index('slug');
            $table->index('start_date');
            $table->index(['is_featured', 'start_date']);
        });

        // Event attendees (RSVP)
        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['going', 'interested', 'not_going'])->default('going');
            $table->string('ticket_number')->nullable();
            $table->boolean('is_checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->index('event_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('ticket_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
    }
};
