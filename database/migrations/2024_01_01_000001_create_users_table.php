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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('role', ['admin', 'moderator', 'verified_developer', 'recruiter', 'member'])->default('member');
            $table->integer('reputation')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->text('ban_reason')->nullable();
            $table->timestamp('banned_until')->nullable();
            $table->string('provider')->nullable(); // google, github
            $table->string('provider_id')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['email', 'username']);
            $table->index('role');
            $table->index('reputation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
