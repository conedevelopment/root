@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Reset password'))

{{-- Content --}}
@section('content')
    <form method="POST" action="{{ URL::route('root.password.reset') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group-wrapper">
            <div class="form-group">
                <label class="form-group__label" for="email">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="email"
                    class="form-group__input @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ $email ?? Request::old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <span class="form-invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-group__label" for="password">{{ __('New password') }}</label>
                <input
                    id="password"
                    type="password"
                    class="form-group__input @error('password') is-invalid @enderror"
                    name="password"
                    required
                    autocomplete="new-password"
                >
                @error('password')
                    <span class="form-invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-group__label" for="password-confirm">{{ __('New password confirmation') }}</label>
                <input
                    id="password-confirm"
                    type="password"
                    class="form-group__input"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                >
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn--secondary">
                    {{ __('Reset password') }}
                </button>
            </div>
        </div>
    </form>
@endsection
