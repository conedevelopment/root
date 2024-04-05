@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Two Factor Authentication'))

{{-- Links --}}
@section('links')
    <a href="{{ URL::route('password.request') }}">{{ __('Reset password') }}</a>
    <a href="{{ URL::route('login') }}">{{ __('Login') }}</a>
@endsection

{{-- Content --}}
@section('content')
<p>{{ __('To finish the two factor authentication, please use the link we sent, or request a new one!') }}</p>
<form method="POST" action="{{ URL::route('root.auth.two-factor.resend') }}">
    @csrf
    <div class="form-group-stack">
        <div class="form-group">
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Resend Two Factor Authentication Link') }}
            </button>
        </div>
    </div>
</form>
@endsection
