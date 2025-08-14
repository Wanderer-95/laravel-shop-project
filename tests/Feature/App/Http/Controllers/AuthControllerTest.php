<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Listeners\SendEmailNewUserListener;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\RequestFactories\SignInFormRequestFactory;
use Tests\RequestFactories\SignUpFormRequestFactory;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_success(): void
    {
        $response = $this->get(action([AuthController::class, 'login']));

        $response
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.index');
    }

    public function test_sign_up_page_success(): void
    {
        $response = $this->get(action([AuthController::class, 'signUp']));

        $response
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    public function test_forgot_page_success(): void
    {
        $response = $this->get(action([AuthController::class, 'forgot']));

        $response
            ->assertOk()
            ->assertSee('Забыли пароль')
            ->assertViewIs('auth.forgot-password');
    }

    public function test_sign_in_success(): void
    {
        $password = '1234';

        $user = User::factory()->create([
            'email' => 'example@mail.ru',
            'password' => Hash::make($password),
        ]);

        $request = SignInFormRequestFactory::new()->create([
            'email' => $user->email,
            'password' => $password
        ]);

        $response = $this->post(action([AuthController::class, 'signIn']), $request);

        $response
            ->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_store_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequestFactory::new()->create([
            'email' => 'example@test.com',
            'password' => '1234',
            'password_confirmation' => '1234'
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => Arr::get($request, 'email'),
        ]);

        $response = $this->post(action([AuthController::class, 'store']), $request);

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => Arr::get($request, 'email'),
        ]);

        $user = User::query()->where('email', Arr::get($request, 'email'))->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }

    public function test_logout_success(): void
    {
        $user = User::factory()->create([
            'email' => 'example@mail.ru'
        ]);

        $response = $this->actingAs($user)->delete(action([AuthController::class, 'logout']));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
