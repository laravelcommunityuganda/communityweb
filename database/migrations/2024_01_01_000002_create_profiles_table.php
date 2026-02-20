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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('title')->nullable(); // Job title
            $table->string('company')->nullable();
            $table->string('location')->nullable(); // District/City in Uganda
            $table->string('github_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('website_url')->nullable();
            $table->json('skills')->nullable(); // Array of skills
            $table->json('social_links')->nullable(); // Other social links
            $table->boolean('is_available_for_work')->default(false);
            $table->boolean('is_available_for_mentoring')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_location')->default(true);
            $table->json('notification_preferences')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('location');
            $table->index('is_available_for_work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
