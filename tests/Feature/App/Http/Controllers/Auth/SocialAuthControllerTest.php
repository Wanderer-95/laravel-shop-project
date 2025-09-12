<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function redirect_returns_socialite_redirect_response()
    {
        // Мокаем провайдера и поведение redirect()
        $providerMock = Mockery::mock(SocialiteProvider::class);
        $providerMock->expects('redirect')
            ->andReturns(redirect('https://github.com/login/oauth/authorize'));

        $this->app->instance(SocialiteFactory::class, Mockery::mock(SocialiteFactory::class, function ($mock) use ($providerMock) {
            $mock->shouldReceive('driver')->with('github')->andReturn($providerMock);
        }));

        $response = $this->get(action([SocialAuthController::class, 'redirect'], ['driver' => 'github']));

        $response->assertRedirect('https://github.com/login/oauth/authorize');
    }

    public function callback_creates_user_and_logs_in_and_redirects_home()
    {
        // Фейковый "GitHub юзер"
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->id = 12345;
        $socialiteUser->email = 'test@example.com';

        // Мокаем provider->user()
        $providerMock = Mockery::mock(SocialiteProvider::class);
        $providerMock->expects('user')->andReturns($socialiteUser);

        $this->app->instance(SocialiteFactory::class, Mockery::mock(SocialiteFactory::class, function ($mock) use ($providerMock) {
            $mock->shouldReceive('driver')->with('github')->andReturn($providerMock);
        }));

        $response = $this->get(action([SocialAuthController::class, 'callback'], ['driver' => 'github']));

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'github_id' => 12345,
            'email' => 'test@example.com',
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals('test@example.com', Auth::user()->email);
    }
}
