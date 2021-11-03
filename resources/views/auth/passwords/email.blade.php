@extends('layouts.auth')

{{-- Title --}}
@section('title', __('Jelszó helyreállítás'))

@section('content')
    <form method="POST" action="{{ URL::route('password.email') }}">
        @csrf
        <div class="form-group-wrapper">
            <div class="form-group">
                <label class="form-group__label" for="email">{{ __('Email cím') }}</label>
                <input
                    id="email"
                    type="email"
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

            <div class="form-group form-group--btn">
                <button type="submit" class="btn btn--secondary">
                    {{ __('Jelszó visszaállító link küldése') }}
                </button>
            </div>
        </div>
@endsection

@section('footer')
    <a href="{{ URL::route('login') }}">
        {{ __('Bejelentkezés') }}
    </a>
    <a href="{{ Config::get('kep.site_url') }}">
        {{ __('Vissza az oldalra') }}
    </a>
@endsection
