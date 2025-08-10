<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');

    Route::post('/login', 'signIn')->name('signIn');

    Route::get('/sign-up', 'signUp')->name('sign-up');

    Route::post('/sign-up', 'store')->name('store');

    Route::delete('/logOut', 'logout')->name('logOut');

    Route::get('/forgot-password', 'forgot')->middleware('guest')->name('password.request');

    Route::post('/forgot-password', 'forgotPassword')->middleware('guest')->name('password.email');

    Route::get('/reset-password/{token}', 'reset')->middleware('guest')->name('password.reset');

    Route::post('/reset-password', 'resetPassword')->middleware('guest')->name('password.update');

    Route::get('/auth/github/socialite/redirect', 'redirectToGithub')->name('github.redirect');

    Route::get('/auth/github/socialite/callback', 'handleGithubCallback')->name('github.callback');
});

Route::get('/', HomeController::class)->name('home');
