<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordFormRequest;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\Factory;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function page(): Factory|View|Application
    {
        return view('auth.forgot-password');
    }

    public function handle(ForgotPasswordFormRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
            ? back()->with(['message' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
