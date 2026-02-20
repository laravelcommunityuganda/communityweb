<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a category for posts
        Category::factory()->create();
    }

    /** @test */
    public function guest_can_list_posts()
    {
        $user = User::factory()->create();
        Post::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'content',
                        'type',
                        'status',
                    ],
                ],
                'meta',
            ]);
    }

    /** @test */
    public function guest_can_view_published_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        $response = $this->getJson("/api/v1/posts/{$post->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                ],
            ]);
    }

    /** @test */
    public function authenticated_user_can_create_post()
    {
        $user = User::factory()->create();
        $category = Category::first();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.',
            'type' => 'question',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Post created successfully',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_can_update_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post updated successfully',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function user_cannot_update_others_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/posts/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Post deleted successfully',
            ]);

        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function guest_cannot_create_post()
    {
        $category = Category::first();

        $response = $this->postJson('/api/v1/posts', [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.',
            'type' => 'question',
            'category_id' => $category->id,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function post_validation_works()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/posts', [
            'title' => '', // Required
            'content' => '', // Required
            'type' => 'invalid', // Invalid type
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content', 'type', 'category_id']);
    }
}