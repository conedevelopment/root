@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Login'))

{{-- Content --}}
@section('content')
    <form method="POST" action="/login">
        @csrf
        <div class="form-group-wrapper">
            <div class="form-group">
                <label class="form-group__label" for="email">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="text"
                    class="form-group__input @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ Request::old('email') }}"
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
                <label class="form-group__label" for="password">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    class="form-group__input @error('password') is-invalid @enderror"
                    name="password"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <span class="form-invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-checkbox">
                    <input
                        type="checkbox"
                        class="custom-checkbox__input"
                        name="remember"
                        id="remember"
                        {{ Request::old('remember') ? 'checked' : '' }}
                    >
                    <label class="custom-checkbox__label" for="remember">
                        {{ __('Remember me') }}
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="submit">
                    {{ __('Login') }}
                </button>
            </div>
        </div>
    </form>
@endsection
