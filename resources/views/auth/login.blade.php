@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Login'))

{{-- Content --}}
@section('content')
    <form method="POST" action="/login">
        @csrf
        <div class="form-group-stack">
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Email') }}</label>
                <input
                    id="email"
                    type="text"
                    class="form-control @error('email') form-control--invalid @enderror"
                    name="email"
                    value="{{ Request::old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <span class="field-feedback field-feedback--invalid" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">{{ __('Password') }}</label>
                <input
                    id="password"
                    type="password"
                    class="form-control @error('password') form-control--invalid @enderror"
                    name="password"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <span class="field-feedback field-feedback--invalid" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-check form-check--lg" for="remember">
                    <input
                        type="checkbox"
                        class="form-check__control"
                        name="remember"
                        id="remember"
                        {{ Request::old('remember') ? 'checked' : '' }}
                    >
                    <span class="form-label form-check__label">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn--primary">
                    {{ __('Login') }}
                </button>
            </div>
        </div>
    </form>
@endsection
