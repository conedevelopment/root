@extends('layouts.auth')

{{-- Top Link --}}
@section('top-link')
    <span class="caption">{{ __('Van már fiókod?') }}</span>
    <a href="{{ URL::route('login') }}">{{ __('Bejelentkezés') }}</a>
@endsection

{{-- Content --}}
@section('content')
    <form class="d-inline" method="POST" action="{{ URL::route('verification.resend') }}" autocomplete="off">
        @csrf

        <div class="form-group">
            <label class="form-group__label" for="email">{{ __('Email') }}</label>
            <input
                id="email"
                type="email"
                class="form-group__input @error('email') is-invalid @enderror"
                name="email"
                value="{{ Request::old('email') }}"
                required
                autofocus
            >
            @error('email')
                <span class="form-invalid-feedback" role="alert">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="form-group mt-2">
            <button type="submit" class="btn btn--secondary">
                {{ __('Link újraküldése') }}
            </button>
        </div>
    </form>
@endsection

{{-- Footer --}}
@section('footer')
    <a href="{{ Config::get('kep.site_url') }}">
        {{ __('Vissza az oldalra') }}
    </a>
@endsection
