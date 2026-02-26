<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function test_can_list_users()
    {
        $this->authenticate();

        User::factory(3)->create()->each(fn($u) => $u->assignRole(Role::findByName('user', 'web')));

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'email', 'roles']]]);
    }

    public function test_can_create_user()
    {
        $this->authenticate();

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'user',
        ];

        $response = $this->postJson('/api/admin/users', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['email' => 'jane@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_can_show_user()
    {
        $this->authenticate();

        $user = User::factory()->create();

        $response = $this->getJson('/api/admin/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => $user->email]);
    }

    public function test_can_update_user()
    {
        $this->authenticate();

        $user = User::factory()->create();

        $response = $this->putJson('/api/admin/users/' . $user->id, ['name' => 'Updated Name']);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_user()
    {
        $this->authenticate();

        $user = User::factory()->create();

        $response = $this->deleteJson('/api/admin/users/' . $user->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_create_user_validation_fails_without_required_fields()
    {
        $this->authenticate();

        $response = $this->postJson('/api/admin/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_unauthenticated_user_cannot_access_users()
    {
        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_users()
    {
        $this->authenticate('user');

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(403);
    }
}
