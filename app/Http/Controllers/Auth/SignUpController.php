<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpFormRequest;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory;
use Illuminate\View\View;

class SignUpController extends Controller
{
    public function page(): Factory|View|Application
    {
        return view('auth.register');
    }

    public function handle(SignUpFormRequest $request, RegisterNewUserContract $action): RedirectResponse
    {
        $action(
            NewUserDTO::fromRequest($request)
        );

        return redirect()->intended(route('home'));
    }
}
