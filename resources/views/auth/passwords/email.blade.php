@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Password reset'))

{{-- Content --}}
@section('content')
    <form method="POST" action="{{ URL::route('root.password.email') }}">
        @csrf
        <div class="form-group-wrapper">
            <div class="form-group">
                <label class="form-group__label" for="email">{{ __('Email') }}</label>
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
                    {{ __('Send reset link') }}
                </button>
            </div>
        </div>
@endsection
