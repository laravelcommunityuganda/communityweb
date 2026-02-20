<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(Request $request)
    {
        $query = Job::with(['user', 'category'])
            ->published()
            ->notExpired();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filter by remote
        if ($request->has('remote')) {
            $query->where('is_remote', $request->boolean('remote'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by skills
        if ($request->has('skills')) {
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            $query->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('required_skills', trim($skill));
                }
            });
        }

        // Filter by salary range
        if ($request->has('salary_min')) {
            $query->where('salary_min', '>=', $request->salary_min);
        }
        if ($request->has('salary_max')) {
            $query->where('salary_max', '<=', $request->salary_max);
        }

        // Sort
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'featured':
                $query->featured()->orderBy('created_at', 'desc');
                break;
            case 'deadline':
                $query->orderBy('deadline', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $jobs = $query->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($jobs);
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:50'],
            'type' => ['required', 'in:full_time,part_time,contract,freelance,internship,remote'],
            'category_id' => ['required', 'exists:categories,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'is_remote' => ['boolean'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'salary_negotiable' => ['boolean'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:50'],
            'experience_years_min' => ['nullable', 'integer', 'min:0'],
            'experience_years_max' => ['nullable', 'integer', 'min:0', 'gte:experience_years_min'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'deadline' => ['required', 'date', 'after:today'],
        ]);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(6);
        $data['status'] = $request->user()->isRecruiter() || $request->user()->isAdmin() ? 'published' : 'pending_approval';

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('companies', 'public');
        }

        $job = Job::create($data);

        ActivityLog::log('job', 'created', $job);

        return response()->json([
            'message' => 'Job posted successfully',
            'job' => $job->load(['user', 'category']),
        ], 201);
    }

    /**
     * Display the specified job.
     */
    public function show(Request $request, Job $job)
    {
        $job->incrementViews();

        $job->load(['user.profile', 'category']);

        $hasApplied = false;
        $isSaved = false;

        if ($request->user()) {
            $hasApplied = $job->applications()->where('user_id', $request->user()->id)->exists();
            $isSaved = $request->user()->savedJobs()->where('job_id', $job->id)->exists();
        }

        return response()->json([
            'job' => $job,
            'has_applied' => $hasApplied,
            'is_saved' => $isSaved,
        ]);
    }

    /**
     * Update the specified job.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'min:50'],
            'type' => ['sometimes', 'in:full_time,part_time,contract,freelance,internship,remote'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'company_name' => ['sometimes', 'string', 'max:255'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'is_remote' => ['boolean'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'gte:salary_min'],
            'salary_currency' => ['nullable', 'string', 'size:3'],
            'salary_negotiable' => ['boolean'],
            'required_skills' => ['nullable', 'array'],
            'required_skills.*' => ['string', 'max:50'],
            'experience_years_min' => ['nullable', 'integer', 'min:0'],
            'experience_years_max' => ['nullable', 'integer', 'min:0', 'gte:experience_years_min'],
            'education_level' => ['nullable', 'string', 'max:255'],
            'deadline' => ['sometimes', 'date', 'after:today'],
        ]);

        $data = $request->all();

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $request->file('company_logo')->store('companies', 'public');
        }

        $job->update($data);

        ActivityLog::log('job', 'updated', $job);

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job->fresh(['user', 'category']),
        ]);
    }

    /**
     * Remove the specified job.
     */
    public function destroy(Request $request, Job $job)
    {
        $this->authorize('delete', $job);

        ActivityLog::log('job', 'deleted', $job);

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully',
        ]);
    }

    /**
     * Apply for a job.
     */
    public function apply(Request $request, Job $job)
    {
        if ($job->isExpired()) {
            return response()->json([
                'message' => 'This job posting has expired',
            ], 400);
        }

        $existingApplication = $job->applications()->where('user_id', $request->user()->id)->exists();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job',
            ], 400);
        }

        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:5000'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'expected_salary' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = [
            'job_id' => $job->id,
            'user_id' => $request->user()->id,
            'cover_letter' => $request->cover_letter,
            'expected_salary' => $request->expected_salary,
        ];

        // Handle CV upload
        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('cvs', 'private');
        }

        $application = JobApplication::create($data);

        $job->increment('applications_count');

        ActivityLog::log('job', 'applied', $job);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    /**
     * Save a job.
     */
    public function save(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->savedJobs()->where('job_id', $job->id)->exists()) {
            return response()->json([
                'message' => 'Job already saved',
            ], 400);
        }

        $user->savedJobs()->attach($job->id);

        return response()->json([
            'message' => 'Job saved successfully',
        ]);
    }

    /**
     * Unsave a job.
     */
    public function unsave(Request $request, Job $job)
    {
        $user = $request->user();

        if (!$user->savedJobs()->where('job_id', $job->id)->exists()) {
            return response()->json([
                'message' => 'Job not saved',
            ], 400);
        }

        $user->savedJobs()->detach($job->id);

        return response()->json([
            'message' => 'Job removed from saved list',
        ]);
    }

    /**
     * Get user's posted jobs.
     */
    public function myJobs(Request $request)
    {
        $jobs = Job::where('user_id', $request->user()->id)
            ->with(['category'])
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($jobs);
    }

    /**
     * Get user's job applications.
     */
    public function myApplications(Request $request)
    {
        $applications = JobApplication::where('user_id', $request->user()->id)
            ->with(['job.user', 'job.category'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->paginatedResponse($applications);
    }

    /**
     * Update application status.
     */
    public function updateApplication(Request $request, JobApplication $application)
    {
        $this->authorize('update', $application);

        $request->validate([
            'status' => ['required', 'in:pending,reviewing,shortlisted,interviewed,offered,hired,rejected'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->updateStatus($request->status);
        
        if ($request->has('notes')) {
            $application->update(['notes' => $request->notes]);
        }

        return response()->json([
            'message' => 'Application status updated',
            'application' => $application->fresh(),
        ]);
    }
}
