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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Employer
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description'); // Markdown content
            $table->enum('type', ['full_time', 'part_time', 'contract', 'freelance', 'internship', 'remote']);
            $table->string('company_name');
            $table->string('company_logo')->nullable();
            $table->string('company_website')->nullable();
            $table->string('location'); // District/City in Uganda
            $table->boolean('is_remote')->default(false);
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('UGX');
            $table->boolean('salary_negotiable')->default(false);
            $table->json('required_skills')->nullable();
            $table->integer('experience_years_min')->nullable();
            $table->integer('experience_years_max')->nullable();
            $table->string('education_level')->nullable();
            $table->date('deadline');
            $table->enum('status', ['draft', 'published', 'pending_approval', 'expired', 'filled'])->default('pending_approval');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('type');
            $table->index('location');
            $table->index('status');
            $table->index('slug');
            $table->index(['is_featured', 'created_at']);
            $table->index('deadline');
        });

        // Job applications
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('cv_path')->nullable();
            $table->json('additional_documents')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->text('notes')->nullable(); // Employer notes
            $table->enum('status', ['pending', 'reviewing', 'shortlisted', 'interviewed', 'offered', 'hired', 'rejected'])->default('pending');
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
            $table->index('job_id');
            $table->index('user_id');
            $table->index('status');
        });

        // Saved jobs
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'job_id']);
            $table->index('user_id');
            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('jobs');
    }
};
