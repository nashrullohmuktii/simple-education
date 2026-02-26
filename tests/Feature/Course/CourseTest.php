<?php

namespace Tests\Feature\Course;

use App\Models\User;
use App\Models\Topic;
use App\Models\Language;
use App\Models\Course;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    private $topic;
    private $language;
    private $instructor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Artisan::call('passport:keys', ['--no-interaction' => true]);
        Artisan::call('passport:client', ['--personal' => true, '--name' => 'Test Client', '--no-interaction' => true]);

        $this->topic = Topic::create(['name' => 'Technology']);
        $this->language = Language::create(['name' => 'English']);
        $this->instructor = User::factory()->create();
    }

    private function authenticate($role = 'admin')
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        Passport::actingAs($user);
        return $user;
    }

    public function test_can_list_courses()
    {
        $this->authenticate();

        Course::create([
            'topic_id' => $this->topic->id,
            'language_id' => $this->language->id,
            'user_id' => $this->instructor->id,
            'title' => 'Laravel Mastery',
            'description' => 'Learn Laravel from scratch.',
            'short_description' => 'A great course on Laravel',
            'price' => 99.99,
            'discount_rate' => 10.00,
            'thumbnail_url' => 'https://example.com/thumb.jpg',
            'level' => 'intermediate',
        ]);

        $response = $this->getJson('/api/courses');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Laravel Mastery']);
    }

    public function test_can_create_course()
    {
        $this->authenticate();

        $payload = [
            'topic_id' => $this->topic->id,
            'language_id' => $this->language->id,
            'user_id' => $this->instructor->id,
            'title' => 'VueJS Advanced',
            'description' => 'VueJS composition API details.',
            'short_description' => 'VueJS Course',
            'price' => 149.99,
            'discount_rate' => 0,
            'thumbnail_url' => 'https://example.com/vue.jpg',
            'level' => 'advance',
        ];

        $response = $this->postJson('/api/courses', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'VueJS Advanced']);

        $this->assertDatabaseHas('courses', ['title' => 'VueJS Advanced']);
    }

    public function test_can_show_course()
    {
        $this->authenticate();

        $course = Course::create([
            'topic_id' => $this->topic->id,
            'language_id' => $this->language->id,
            'user_id' => $this->instructor->id,
            'title' => 'React Native',
            'description' => 'Build apps with React Native.',
            'short_description' => 'React Native Course',
            'price' => 199.99,
            'discount_rate' => 5.5,
            'thumbnail_url' => 'https://example.com/rn.jpg',
            'level' => 'beginner',
        ]);

        $response = $this->getJson('/api/courses/' . $course->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'React Native']);
    }

    public function test_can_update_course()
    {
        $this->authenticate();

        $course = Course::create([
            'topic_id' => $this->topic->id,
            'language_id' => $this->language->id,
            'user_id' => $this->instructor->id,
            'title' => 'Angular Basics',
            'description' => 'Learn Angular basics.',
            'short_description' => 'Angular Course',
            'price' => 50.00,
            'discount_rate' => 0,
            'thumbnail_url' => 'https://example.com/angular.jpg',
            'level' => 'all',
        ]);

        $payload = [
            'title' => 'Angular Intermediate',
            'price' => 75.00,
        ];

        $response = $this->putJson('/api/courses/' . $course->id, $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Angular Intermediate', 'price' => 75.00]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Angular Intermediate',
            'price' => 75.00,
        ]);
    }

    public function test_can_delete_course()
    {
        $this->authenticate();

        $course = Course::create([
            'topic_id' => $this->topic->id,
            'language_id' => $this->language->id,
            'user_id' => $this->instructor->id,
            'title' => 'To be deleted',
            'description' => 'Will be deleted.',
            'short_description' => 'Delete Route',
            'price' => 0,
            'discount_rate' => 0,
            'thumbnail_url' => 'https://example.com/delete.jpg',
            'level' => 'all',
        ]);

        $response = $this->deleteJson('/api/courses/' . $course->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_create_course_validation_fails_without_required_fields()
    {
        $this->authenticate();

        $response = $this->postJson('/api/courses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['topic_id', 'language_id', 'user_id', 'title', 'price']);
    }

    public function test_unauthenticated_user_cannot_access_courses()
    {
        $response = $this->getJson('/api/courses');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_courses()
    {
        $this->authenticate('user');

        $response = $this->getJson('/api/courses');

        $response->assertStatus(403);
    }
}
