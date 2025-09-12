<?php

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrarContract;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class AuthRegistrar implements RouteRegistrarContract
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {

            Route::controller(SignInController::class)->group(function () {
                Route::get('/login', 'page')->name('login');
                Route::post('/login', 'handle')
                    ->middleware(['guest'])
                    ->name('login.handle');
                Route::delete('/logOut', 'logout')->name('logOut');
            });

            Route::controller(SignUpController::class)->group(function () {
                Route::get('/register', 'page')->name('register');
                Route::post('/sign-up', 'handle')
                    ->middleware(['guest'])
                    ->name('register.handle');
            });

            Route::controller(ForgotPasswordController::class)->group(function () {
                Route::get('/forgot-password', 'page')->middleware('guest')->name('forgot');
                Route::post('/forgot-password', 'handle')->middleware('guest')->name('forgot.handle');
            });

            Route::controller(ResetPasswordController::class)->group(function () {
                Route::get('/reset-password/{token}', 'page')->middleware('guest')->name('password.reset');
                Route::post('/reset-password', 'handle')->middleware('guest')->name('password-reset.handle');
            });

            Route::controller(SocialAuthController::class)->group(function () {
                Route::get('/auth/{driver}/socialite/redirect', 'redirect')->name('redirect');
                Route::get('/auth/{driver}/socialite/callback', 'callback')->name('callback');
            });

        });
    }
}
