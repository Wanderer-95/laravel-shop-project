<?php

namespace App\Http\Controllers\Auth;

use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\RequestFactories\SignUpFormRequestFactory;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_success(): void
    {
        $response = $this->get(action([SignUpController::class, 'page']));

        $response
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.register');
    }

    public function test_register_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequestFactory::new()->create([
            'email' => 'example@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => Arr::get($request, 'email'),
        ]);

        $response = $this->post(action([SignUpController::class, 'handle']), $request);

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => Arr::get($request, 'email'),
        ]);

        $user = User::query()->where('email', Arr::get($request, 'email'))->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener;
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }
}
