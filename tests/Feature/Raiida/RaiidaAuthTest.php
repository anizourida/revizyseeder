<?php

namespace Tests\Feature\Raiida;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class RaiidaAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_bearer_token_and_user_payload(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'operator@example.com',
            'password' => 'Secret123!',
            'device_name' => 'web-dashboard',
        ]);

        $response->assertOk()->assertJsonStructure([
            'token_type',
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);

        $this->assertSame('Bearer', $response->json('token_type'));
        $this->assertSame($user->id, $response->json('user.id'));
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        User::query()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'operator@example.com',
            'password' => 'WrongPassword',
        ])->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_auth_me_returns_authenticated_user(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $token = $user->createToken('web')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJson([
                'id' => $user->id,
                'name' => 'Operator',
                'email' => 'operator@example.com',
                'role' => 'operator',
            ]);
    }

    public function test_logout_revokes_current_access_token(): void
    {
        $user = User::query()->create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => bcrypt('Secret123!'),
        ]);

        $token = $user->createToken('web')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me')
            ->assertUnauthorized();

        $this->assertSame(0, PersonalAccessToken::query()->count());
    }
}
