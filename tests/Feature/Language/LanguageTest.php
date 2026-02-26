<?php

namespace Tests\Feature\Language;

use App\Models\User;
use App\Models\Language;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LanguageTest extends TestCase
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

    public function test_can_list_languages()
    {
        $this->authenticate();

        Language::create(['name' => 'English']);
        Language::create(['name' => 'Spanish']);

        $response = $this->getJson('/api/languages');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'English'])
            ->assertJsonFragment(['name' => 'Spanish']);
    }

    public function test_can_create_language()
    {
        $this->authenticate();

        $payload = [
            'name' => 'French',
        ];

        $response = $this->postJson('/api/languages', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'French']);

        $this->assertDatabaseHas('languages', ['name' => 'French']);
    }

    public function test_can_show_language()
    {
        $this->authenticate();

        $language = Language::create(['name' => 'German']);

        $response = $this->getJson('/api/languages/' . $language->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'German']);
    }

    public function test_can_update_language()
    {
        $this->authenticate();

        $language = Language::create(['name' => 'Old Name']);

        $payload = [
            'name' => 'New Name',
        ];

        $response = $this->putJson('/api/languages/' . $language->id, $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Name']);

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'name' => 'New Name',
        ]);
    }

    public function test_can_delete_language()
    {
        $this->authenticate();

        $language = Language::create(['name' => 'To be deleted']);

        $response = $this->deleteJson('/api/languages/' . $language->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }

    public function test_create_language_validation_fails_without_name()
    {
        $this->authenticate();

        $response = $this->postJson('/api/languages', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_unauthenticated_user_cannot_access_languages()
    {
        $response = $this->getJson('/api/languages');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_languages()
    {
        $this->authenticate('user');

        $response = $this->getJson('/api/languages');

        $response->assertStatus(403);
    }
}
