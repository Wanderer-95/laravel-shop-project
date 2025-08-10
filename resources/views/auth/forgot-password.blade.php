@extends('layouts.auth')

@section('title', 'Забыли пароль')

@section('content')

    <x-form.auth-forms title="Забыли пароль" action="{{ route('password.email') }}" method="POST">
        <x-form.text-input type="email" placeholder="E-mail" name="email" required is-error="{{ $errors->has('email') }}"/>
        @error('email')
        <x-form.error>
            {{ $message }}
        </x-form.error>
        @enderror

        <x-form.primary-button>Отправить</x-form.primary-button>

        <x-slot:socialAuth></x-slot:socialAuth>
        <x-slot:buttons>
            <div class="space-y-3 mt-5">
                <div class="text-xxs md:text-xs"><a href="{{ route('login') }}" class="text-white hover:text-white/70 font-bold">Вспомнил пароль</a></div>
            </div>
        </x-slot:buttons>
    </x-form.auth-forms>

@endsection
