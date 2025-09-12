<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $driver): RedirectResponse
    {
        try {
            return Socialite::driver($driver)->redirect();
        } catch (\Throwable $e) {
            throw new DomainException('Произошла ошибка или драйвер не поддерживается!');
        }
    }

    public function callback(string $driver): RedirectResponse
    {
        if ($driver !== 'github') {
            throw new DomainException('Драйвер не поддерживается!');
        }

        $githubUser = Socialite::driver($driver)->user();

        $user = User::updateOrCreate([
            $driver.'_id' => $githubUser->id,
        ], [
            'name' => 'Test',
            'email' => $githubUser->email,
            'password' => Hash::make(Str::random(20)),
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
