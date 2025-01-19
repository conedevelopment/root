@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Login'))

{{-- Content --}}
@section('content')
<p>{{ __('Hey there, welcome back!') }}</p>
<form method="POST" action="{{ URL::route('root.auth.login') }}" data-turbo="false">
    @csrf
    <div class="form-group-stack">
        <div class="form-group">
            <label class="form-label" for="email">{{ __('Email') }}</label>
            <input
                @class(['form-control', 'form-control--lg', 'form-control--invalid' => $errors->has('email')])
                id="email"
                type="email"
                name="email"
                required
                value="{{ Request::old('email') }}"
            >
            @error('email')
                <span class="field-feedback field-feedback--invalid">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label form-label--space-between" for="password">
                {{ __('Password') }}
                <a href="{{ URL::route('root.auth.password.request') }}">{{ __('Forgot your password?') }}</a>
            </label>
            <input
                @class(['form-control', 'form-control--lg', 'form-control--invalid' => $errors->has('password')])
                id="password"
                type="password"
                name="password"
                required
            >
            @error('password')
                <span class="field-feedback field-feedback--invalid">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-check form-check--lg" for="remember">
                <input class="form-check__control" id="remember" type="checkbox" name="remember" value="1">
                <span class="form-label form-check__label">{{ __('Remember me') }}</span>
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Login') }}
            </button>
        </div>
    </div>
</form>
@endsection
