@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Two Factor Authentication'))

{{-- Content --}}
@section('content')
<p>{{ __('To finish the two factor authentication, please add the verification code, or request a new one!') }}</p>
<form method="POST" action="{{ URL::route('root.auth.two-factor.verify') }}">
    @csrf
    <div class="form-group-stack">
        <div class="form-group">
            <label class="form-label" for="email">{{ __('Code') }}</label>
            <input
                @class(['form-control', 'form-control--lg', 'form-control--invalid' => $errors->has('code')])
                id="code"
                type="number"
                name="code"
                required
                value="{{ Request::old('code', $code) }}"
            >
            @error('code')
                <span class="field-feedback field-feedback--invalid">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-check form-check--lg" for="trust">
                <input class="form-check__control" id="trust" type="checkbox" name="trust" value="1">
                <span class="form-label form-check__label">{{ __('Trust in this browser') }}</span>
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                {{ __('Verify') }}
            </button>
        </div>
    </div>
</form>
@endsection

{{-- Footer --}}
@section('footer')
<form method="POST" action="{{ URL::route('root.auth.logout') }}">
    @csrf
    <div class="form-group">
        <button type="submit" class="btn btn--light btn--sm">
            {{ __('Logout') }}
        </button>
    </div>
</form>

<form method="POST" action="{{ URL::route('root.auth.two-factor.resend') }}">
    @csrf
    <div class="form-group">
        <button type="submit" class="btn btn--light btn--sm">
            {{ __('Resend Verification Code') }}
        </button>
    </div>
</form>
@endsection
