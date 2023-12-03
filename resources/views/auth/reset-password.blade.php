@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Reset Password'))

{{-- Content --}}
@section('content')
<form method="POST" method="POST" action="{{ URL::route('root.auth.password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
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
            <label class="form-label" for="password">{{ __('Password') }}</label>
            <input
                @class(['form-control', 'form-control--lg', 'form-control--invalid' => $errors->has('password')])
                id="password"
                type="password"
                name="password"
                required
                value="{{ Request::old('password') }}"
            >
            @error('password')
                <span class="field-feedback field-feedback--invalid">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">{{ __('Password Confirmation') }}</label>
            <input
                @class(['form-control', 'form-control--lg', 'form-control--invalid' => $errors->has('password_confirmation')])
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                value="{{ Request::old('password_confirmation') }}"
            >
            @error('password_confirmation')
                <span class="field-feedback field-feedback--invalid">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Reset Password') }}
            </button>
        </div>
    </div>
</form>
@endsection
