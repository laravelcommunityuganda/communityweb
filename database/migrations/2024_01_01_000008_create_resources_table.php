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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'github_repo', 'youtube', 'code_snippet', 'boilerplate', 'other']);
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('external_url')->nullable(); // For GitHub, YouTube links
            $table->text('code_content')->nullable(); // For code snippets
            $table->string('code_language')->nullable(); // For code snippets
            $table->integer('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('downloads_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->enum('status', ['draft', 'published', 'pending_review', 'rejected'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('type');
            $table->index('status');
            $table->index('slug');
            $table->index(['is_featured', 'created_at']);
        });

        // Resource ratings
        Schema::create('resource_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1-5
            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'resource_id']);
            $table->index('user_id');
            $table->index('resource_id');
        });

        // Resource bookmarks
        Schema::create('resource_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'resource_id']);
            $table->index('user_id');
            $table->index('resource_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_bookmarks');
        Schema::dropIfExists('resource_ratings');
        Schema::dropIfExists('resources');
    }
};
