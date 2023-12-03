@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Forgot Password'))

{{-- Content --}}
@section('content')
<form method="POST" action="{{ URL::route('root.auth.password.email') }}">
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
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Send Password Reset Email') }}
            </button>
        </div>
    </div>
</form>
@endsection
