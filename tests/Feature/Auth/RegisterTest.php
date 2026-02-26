<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the roles so 'user' role exists
        $this->seed(RoleSeeder::class);

        // Install passport for token generation
        Artisan::call('passport:keys', ['--no-interaction' => true]);
        Artisan::call('passport:client', ['--personal' => true, '--name' => 'Test Personal Access Client', '--no-interaction' => true]);
    }

    public function test_user_can_register_successfully()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_registration_fails_with_missing_fields()
    {
        $payload = [
            'name' => 'John Doe',
            // Missing email and password
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_duplicate_email()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $payload = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_fails_with_short_password()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'short', // less than 8 characters
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
