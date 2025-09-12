<?php

namespace App\Http\Controllers\Auth;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\RequestFactories\SignInFormRequestFactory;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_success(): void
    {
        $response = $this->get(action([SignInController::class, 'page']));

        $response
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    public function test_login_success(): void
    {
        $password = '1234';

        $user = UserFactory::new()->create([
            'email' => 'example@mail.ru',
            'password' => Hash::make($password),
        ]);

        $request = SignInFormRequestFactory::new()->create([
            'email' => $user->email,
            'password' => $password,
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response
            ->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_success(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'example@mail.ru',
        ]);

        $response = $this->actingAs($user)->delete(action([SignInController::class, 'logout']));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
