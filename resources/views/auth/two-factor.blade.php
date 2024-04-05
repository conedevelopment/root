@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Two Factor Authentication'))

{{-- Content --}}
@section('content')
<p>{{ __('To finish the two factor authentication, please use the link we sent, or request a new one!') }}</p>
<form method="POST" action="{{ URL::route('root.auth.two-factor.resend') }}">
    @csrf
    <div class="form-group-stack">
        <div class="form-group">
            <button type="submit" class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Resend Two Factor Authentication Link') }}
            </button>
        </div>
    </div>
</form>
<span class="or-separator" aria-hidden="true">{{ __('or') }}</span>
<form method="POST" action="{{ URL::route('root.auth.logout') }}">
    @csrf
    <div class="form-group">
        <button type="submit" class="btn btn--light btn--sm">
            {{ __('Logout') }}
        </button>
    </div>
</form>
@endsection
