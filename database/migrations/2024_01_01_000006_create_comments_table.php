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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->text('content'); // Markdown content
            $table->integer('upvotes_count')->default(0);
            $table->integer('downvotes_count')->default(0);
            $table->boolean('is_best_answer')->default(false);
            $table->integer('depth')->default(0); // For nested replies
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('post_id');
            $table->index('parent_id');
            $table->index(['post_id', 'created_at']);
            $table->index(['is_best_answer', 'post_id']);
        });

        // Add foreign key constraint for posts.best_answer_id after comments table exists
        Schema::table('posts', function (Blueprint $table) {
            $table->foreign('best_answer_id')->references('id')->on('comments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['best_answer_id']);
        });
        Schema::dropIfExists('comments');
    }
};
