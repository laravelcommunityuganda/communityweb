<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Job;
use App\Models\Event;
use App\Models\Badge;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view posts', 'create posts', 'edit posts', 'delete posts', 'moderate posts',
            'view jobs', 'create jobs', 'edit jobs', 'delete jobs', 'approve jobs',
            'view events', 'create events', 'edit events', 'delete events',
            'view resources', 'create resources', 'edit resources', 'delete resources', 'approve resources',
            'view reports', 'resolve reports',
            'manage categories', 'manage badges', 'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $moderatorRole->givePermissionTo([
            'view users', 'edit users',
            'view posts', 'edit posts', 'delete posts', 'moderate posts',
            'view jobs', 'approve jobs',
            'view events', 'edit events', 'delete events',
            'view resources', 'approve resources',
            'view reports', 'resolve reports',
        ]);

        $verifiedDeveloperRole = Role::firstOrCreate(['name' => 'verified_developer']);
        $verifiedDeveloperRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts', 'delete posts',
            'view jobs', 'create jobs', 'edit jobs',
            'view events', 'create events', 'edit events',
            'view resources', 'create resources', 'edit resources',
        ]);

        $recruiterRole = Role::firstOrCreate(['name' => 'recruiter']);
        $recruiterRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts',
            'view jobs', 'create jobs', 'edit jobs',
            'view events', 'create events',
        ]);

        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $memberRole->givePermissionTo([
            'view posts', 'create posts', 'edit posts',
            'view jobs',
            'view events',
            'view resources', 'create resources',
        ]);

        // Create admin user
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@laravelcommunity.ug',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        Profile::firstOrCreate(['user_id' => $admin->id]);

        // Create moderator
        $moderator = User::firstOrCreate(
            ['username' => 'moderator'],
            [
                'name' => 'Moderator User',
                'email' => 'moderator@laravelcommunity.ug',
                'password' => Hash::make('password'),
                'role' => 'moderator',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );
        $moderator->assignRole('moderator');
        Profile::firstOrCreate(['user_id' => $moderator->id]);

        // Create sample users
        $users = [];
        $ugandaDistricts = ['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Mbale', 'Arua', 'Lira', 'Masaka', 'Soroti'];
        $skills = ['PHP', 'Laravel', 'Vue.js', 'React', 'JavaScript', 'Python', 'Django', 'Node.js', 'MySQL', 'PostgreSQL', 'Docker', 'AWS', 'Git', 'CSS', 'Tailwind CSS', 'Bootstrap'];

        // Check if we already have sample users - create fewer users to avoid timeout
        if (User::count() <= 2) {
            for ($i = 1; $i <= 10; $i++) {
                $user = User::create([
                    'name' => fake()->name(),
                    'username' => fake()->unique()->userName() . $i,
                    'email' => fake()->unique()->safeEmail(),
                    'password' => Hash::make('password'),
                    'role' => 'member',
                    'reputation' => rand(0, 1000),
                    'is_verified' => rand(0, 1),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('member');

                Profile::create([
                    'user_id' => $user->id,
                    'bio' => fake()->paragraph(),
                    'title' => fake()->jobTitle(),
                    'company' => fake()->company(),
                    'location' => $ugandaDistricts[array_rand($ugandaDistricts)],
                    'github_url' => 'https://github.com/' . $user->username,
                    'skills' => array_rand(array_flip($skills), rand(3, 6)),
                    'is_available_for_work' => rand(0, 1),
                ]);

                $users[] = $user;
            }
        } else {
            $users = User::where('role', 'member')->limit(10)->get()->all();
        }

        // Create categories
        $categoriesData = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'description' => 'Laravel PHP Framework discussions', 'icon' => 'laravel', 'color' => '#FF2D20'],
            ['name' => 'PHP', 'slug' => 'php', 'description' => 'PHP programming language', 'icon' => 'php', 'color' => '#777BB4'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'JavaScript programming', 'icon' => 'javascript', 'color' => '#F7DF1E'],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'description' => 'Vue.js framework discussions', 'icon' => 'vuejs', 'color' => '#4FC08D'],
            ['name' => 'React', 'slug' => 'react', 'description' => 'React library discussions', 'icon' => 'react', 'color' => '#61DAFB'],
            ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'DevOps practices and tools', 'icon' => 'devops', 'color' => '#326CE5'],
            ['name' => 'AI & Machine Learning', 'slug' => 'ai-ml', 'description' => 'AI and ML discussions', 'icon' => 'ai', 'color' => '#FF6F00'],
            ['name' => 'Startups', 'slug' => 'startups', 'description' => 'Startup discussions and advice', 'icon' => 'startup', 'color' => '#00D4AA'],
            ['name' => 'Jobs', 'slug' => 'jobs', 'description' => 'Job postings and career advice', 'icon' => 'briefcase', 'color' => '#6366F1'],
            ['name' => 'Hosting', 'slug' => 'hosting', 'description' => 'Web hosting discussions', 'icon' => 'server', 'color' => '#8B5CF6'],
        ];

        foreach ($categoriesData as $categoryData) {
            Category::firstOrCreate(['slug' => $categoryData['slug']], $categoryData);
        }

        // Create tags
        $tagNames = ['eloquent', 'blade', 'api', 'authentication', 'testing', 'deployment', 'docker', 'nginx', 'redis', 'queues', 'events', 'middleware', 'validation', 'migrations', 'seeding', 'factories'];

        foreach ($tagNames as $tagName) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                [
                    'name' => $tagName,
                    'usage_count' => rand(0, 50),
                ]
            );
        }

        // Create sample posts if none exist
        if (Post::count() === 0 && count($users) > 0) {
            $postTitles = [
                'How to set up Laravel with Vue.js?',
                'Best practices for API authentication in Laravel',
                'Understanding Eloquent relationships',
                'Deploying Laravel to DigitalOcean',
                'Laravel queues and Redis setup',
                'Testing Laravel applications with Pest',
                'Vue 3 Composition API tutorial',
                'Building a REST API with Laravel',
                'Laravel Sanctum vs Passport',
                'Optimizing Laravel performance',
            ];

            foreach ($postTitles as $title) {
                $user = $users[array_rand($users)];
                $category = Category::inRandomOrder()->first();

                $post = Post::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(6),
                    'content' => fake()->paragraphs(5, true),
                    'type' => ['question', 'discussion', 'tutorial'][rand(0, 2)],
                    'status' => 'published',
                    'views_count' => rand(10, 500),
                    'upvotes_count' => rand(0, 50),
                    'downvotes_count' => rand(0, 10),
                    'comments_count' => rand(0, 20),
                ]);

                // Attach random tags
                $post->tags()->attach(Tag::inRandomOrder()->limit(rand(2, 4))->pluck('id'));
            }
        }

        // Create sample jobs if none exist
        if (Job::count() === 0 && count($users) > 0) {
            $jobTitles = ['Senior Laravel Developer', 'Full Stack Developer', 'Frontend Developer', 'Backend Developer', 'DevOps Engineer', 'Software Engineer', 'PHP Developer', 'Vue.js Developer'];

            foreach ($jobTitles as $title) {
                $user = $users[array_rand($users)];

                Job::create([
                    'user_id' => $user->id,
                    'category_id' => Category::inRandomOrder()->first()->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(6),
                    'description' => fake()->paragraphs(3, true),
                    'type' => ['full_time', 'part_time', 'contract', 'remote'][rand(0, 3)],
                    'company_name' => fake()->company(),
                    'company_website' => fake()->url(),
                    'location' => $ugandaDistricts[array_rand($ugandaDistricts)],
                    'is_remote' => rand(0, 1),
                    'salary_min' => rand(500000, 2000000),
                    'salary_max' => rand(2000000, 5000000),
                    'salary_currency' => 'UGX',
                    'required_skills' => array_rand(array_flip($skills), rand(3, 5)),
                    'deadline' => now()->addDays(rand(7, 30)),
                    'status' => 'published',
                    'views_count' => rand(10, 200),
                ]);
            }
        }

        // Create sample events if none exist
        if (Event::count() === 0 && count($users) > 0) {
            $eventTitles = ['Laravel Meetup Kampala', 'Vue.js Conference Uganda', 'PHP Developers Summit', 'Tech Talk: AI in Africa', 'DevOps Day Uganda'];

            foreach ($eventTitles as $title) {
                $user = $users[array_rand($users)];

                Event::create([
                    'user_id' => $user->id,
                    'category_id' => Category::inRandomOrder()->first()->id,
                    'title' => $title,
                    'slug' => Str::slug($title) . '-' . Str::random(6),
                    'description' => fake()->paragraphs(2, true),
                    'type' => ['meetup', 'workshop', 'conference', 'webinar'][rand(0, 3)],
                    'format' => ['physical', 'online', 'hybrid'][rand(0, 2)],
                    'venue_name' => fake()->company(),
                    'venue_address' => fake()->address(),
                    'venue_city' => $ugandaDistricts[array_rand($ugandaDistricts)],
                    'start_date' => now()->addDays(rand(7, 60)),
                    'end_date' => now()->addDays(rand(61, 65)),
                    'capacity' => rand(50, 500),
                    'is_free' => rand(0, 1),
                    'status' => 'published',
                ]);
            }
        }

        // Create badges
        $badges = [
            ['name' => 'First Post', 'slug' => 'first-post', 'description' => 'Created your first post', 'type' => 'achievement', 'points' => 10, 'icon' => 'post', 'criteria' => ['posts_count' => 1]],
            ['name' => 'Helpful', 'slug' => 'helpful', 'description' => 'Received 10 upvotes', 'type' => 'achievement', 'points' => 20, 'icon' => 'thumb-up', 'criteria' => ['upvotes_count' => 10]],
            ['name' => 'Problem Solver', 'slug' => 'problem-solver', 'description' => 'Answer accepted 5 times', 'type' => 'achievement', 'points' => 50, 'icon' => 'check-circle', 'criteria' => ['accepted_answers' => 5]],
            ['name' => 'Laravel Expert', 'slug' => 'laravel-expert', 'description' => 'Contributed 50 Laravel posts', 'type' => 'skill', 'points' => 100, 'icon' => 'laravel', 'criteria' => ['category_posts' => 50]],
            ['name' => 'Community Leader', 'slug' => 'community-leader', 'description' => 'Top 10 reputation', 'type' => 'special', 'points' => 200, 'icon' => 'star', 'criteria' => ['reputation_rank' => 10]],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }

        $this->command->info('Database seeded successfully!');
    }
}
