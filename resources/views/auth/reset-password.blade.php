@extends('layouts.auth')

@section('title', 'Восстановление пароля')

@section('content')

    <x-form.auth-forms title="Восстановление пароля" action="{{ route('password-reset.handle') }}" method="POST">
        <input type="hidden" name="token" value="{{ $token }}">

        <x-form.text-input type="email" placeholder="E-mail" value="{{ request('email') }}" name="email" required is-error="{{ $errors->has('email') }}"/>
        @error('email')
        <x-form.error>
            {{ $message }}
        </x-form.error>
        @enderror

        <x-form.text-input type="password" placeholder="Введите пароль" name="password" required
                           is-error="{{ $errors->has('password') }}"/>
        @error('password')
        <x-form.error>
            {{ $message }}
        </x-form.error>
        @enderror
        <x-form.text-input type="password" placeholder="Подтвердите пароль" name="password_confirmation" required
                           is-error="{{ $errors->has('password_confirmation') }}"/>
        @error('password_confirmation')
        <x-form.error>
            {{ $message }}
        </x-form.error>
        @enderror

        <x-form.primary-button>Обновить пароль</x-form.primary-button>

        <x-slot:socialAuth></x-slot:socialAuth>
        <x-slot:buttons></x-slot:buttons>
    </x-form.auth-forms>

@endsection
