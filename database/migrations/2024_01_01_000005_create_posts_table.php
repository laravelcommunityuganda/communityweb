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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content'); // Markdown content
            $table->text('excerpt')->nullable();
            $table->enum('type', ['question', 'discussion', 'announcement', 'tutorial'])->default('question');
            $table->enum('status', ['draft', 'published', 'archived', 'pending_review'])->default('published');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('upvotes_count')->default(0);
            $table->integer('downvotes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('bookmarks_count')->default(0);
            $table->unsignedBigInteger('best_answer_id')->nullable();
            $table->timestamp('solved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('slug');
            $table->index('type');
            $table->index('status');
            $table->index(['is_pinned', 'created_at']);
            $table->index(['upvotes_count', 'created_at']);
        });

        // Post-Tag pivot table
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'tag_id']);
            $table->index('post_id');
            $table->index('tag_id');
        });

        // Bookmarked posts
        Schema::create('post_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'post_id']);
            $table->index('user_id');
            $table->index('post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_bookmarks');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('posts');
    }
};
