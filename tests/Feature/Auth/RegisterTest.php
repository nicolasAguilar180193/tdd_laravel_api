<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiHelpers();
    }

    /** @test */
    public function can_register(): void
    {
        $response = $this->postJson(
            route('api.v1.register'),
            $data = $this->validCredentials()
        );

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain token is invalid'
        );

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_register_again(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('api.v1.register'))
            ->assertNoContent();
    }

    /** @test */
    public function name_is_required(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'name' => '',
            ])
        )->assertJsonValidationErrorFor('name');
    }

    /** @test */
    public function email_is_required(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'email' => '',
            ])
        )->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_a_valid_email(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'email' => 'invalid-email',
            ])
        )->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function email_must_be_unique(): void
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'email' => $user->email,
            ])
        )->assertJsonValidationErrorFor('email');
    }

    /** @test */
    public function password_is_required(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'password' => '',
            ])
        )->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function password_must_be_confirmed(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'password' => 'password',
                'password_confirmation' => 'not-confirmed',
            ])
        )->assertJsonValidationErrorFor('password');
    }

    /** @test */
    public function device_name_is_required(): void
    {
        $this->postJson(route('api.v1.register'),
            $data = $this->validCredentials([
                'device_name' => '',
            ])
        )->assertJsonValidationErrorFor('device_name');
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'name' => 'Nicolas',
            'email' => 'nicolas@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'My device',
        ], $overrides);
    }
}
