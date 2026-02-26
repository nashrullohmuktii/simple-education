<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        Artisan::call('passport:keys', ['--no-interaction' => true]);
        Artisan::call('passport:client', ['--personal' => true, '--name' => 'Test Personal Access Client', '--no-interaction' => true]);
    }

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'johndoe@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
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
    }

    public function test_login_fails_with_incorrect_password()
    {
        $user = User::factory()->create([
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $payload = [
            'email' => 'johndoe@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'The provided credentials do not match our records.',
            ]);
    }

    public function test_login_fails_with_unregistered_email()
    {
        $payload = [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'The provided credentials do not match our records.',
            ]);
    }

    public function test_login_fails_with_missing_fields()
    {
        $payload = []; // Missing email and password

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
