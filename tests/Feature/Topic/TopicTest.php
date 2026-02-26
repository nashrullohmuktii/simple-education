<?php

namespace Tests\Feature\Topic;

use App\Models\User;
use App\Models\Topic;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Artisan::call('passport:keys', ['--no-interaction' => true]);
        Artisan::call('passport:client', ['--personal' => true, '--name' => 'Test Client', '--no-interaction' => true]);
    }

    private function authenticate($role = 'admin')
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        Passport::actingAs($user);
        return $user;
    }

    public function test_can_list_topics()
    {
        $this->authenticate();

        Topic::create(['name' => 'Math', 'description' => 'Mathematics topic']);
        Topic::create(['name' => 'Science', 'description' => 'Science topic']);

        $response = $this->getJson('/api/topics');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'Math'])
            ->assertJsonFragment(['name' => 'Science']);
    }

    public function test_can_create_topic()
    {
        $this->authenticate();

        $payload = [
            'name' => 'History',
            'description' => 'History lessons',
            'parent_id' => null,
        ];

        $response = $this->postJson('/api/topics', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'History']);

        $this->assertDatabaseHas('topics', ['name' => 'History']);
    }

    public function test_can_show_topic()
    {
        $this->authenticate();

        $topic = Topic::create(['name' => 'Geography']);

        $response = $this->getJson('/api/topics/' . $topic->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Geography']);
    }

    public function test_can_update_topic()
    {
        $this->authenticate();

        $topic = Topic::create(['name' => 'Old Name', 'description' => 'Old description']);

        $payload = [
            'name' => 'New Name',
        ];

        $response = $this->putJson('/api/topics/' . $topic->id, $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'name' => 'New Name',
            'description' => 'Old description',
        ]);
    }

    public function test_can_delete_topic()
    {
        $this->authenticate();

        $topic = Topic::create(['name' => 'To be deleted']);

        $response = $this->deleteJson('/api/topics/' . $topic->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('topics', ['id' => $topic->id]);
    }

    public function test_create_topic_validation_fails_without_name()
    {
        $this->authenticate();

        $response = $this->postJson('/api/topics', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_access_topics()
    {
        $response = $this->getJson('/api/topics');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_topics()
    {
        $this->authenticate('user');

        $response = $this->getJson('/api/topics');

        $response->assertStatus(403);
    }
}
