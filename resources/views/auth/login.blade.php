@extends('root::auth.layout')

{{-- Title --}}
@section('title', __('Login'))

{{-- Content --}}
@section('content')
<p>Hey there, welcome back!</p>
<form method="POST" action="#">
    <div class="form-group-stack">
        <div class="social-logins">
            <button class="btn btn--outline-dark btn--lg btn--icon btn--block">
                <svg aria-hidden='true' focusable='false' height='100%' version='1.1' viewBox='0 0 24 24' width='100%' xml:space='preserve' style='fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <g>
                        <path d='M12.24,9.818l-0,4.647l6.458,0c-0.283,1.495 -1.135,2.76 -2.411,3.611l3.895,3.022c2.269,-2.094 3.578,-5.171 3.578,-8.825c-0,-0.851 -0.076,-1.669 -0.218,-2.455l-11.302,0Z' style='fill:#4285f4;fill-rule:nonzero;'></path>
                        <path d='M5.515,14.284l-0.879,0.672l-3.109,2.422c1.975,3.917 6.022,6.622 10.713,6.622c3.24,-0 5.956,-1.069 7.941,-2.902l-3.894,-3.022c-1.069,0.72 -2.433,1.157 -4.047,1.157c-3.12,-0 -5.771,-2.106 -6.72,-4.942l-0.005,-0.007Z' style='fill:#34a853;fill-rule:nonzero;'></path>
                        <path d='M1.527,6.622c-0.818,1.614 -1.287,3.436 -1.287,5.378c0,1.942 0.469,3.764 1.287,5.378c0,0.011 3.993,-3.098 3.993,-3.098c-0.24,-0.72 -0.382,-1.484 -0.382,-2.28c0,-0.797 0.142,-1.56 0.382,-2.28l-3.993,-3.098Z' style='fill:#fbbc05;fill-rule:nonzero;'></path>
                        <path d='M12.24,4.778c1.767,0 3.338,0.611 4.593,1.789l3.436,-3.436c-2.084,-1.942 -4.789,-3.131 -8.029,-3.131c-4.691,0 -8.738,2.695 -10.713,6.622l3.993,3.098c0.949,-2.836 3.6,-4.942 6.72,-4.942Z' style='fill:#ea4335;fill-rule:nonzero;'></path>
                    </g>
                </svg>
                Sign in with Google
            </button>
            <button class="btn btn--outline-dark btn--lg btn--icon btn--block">
                <svg aria-hidden='true' focusable='false' height='100%' style='fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;fill:currentColor;' version='1.1' viewBox='0 0 15 18' width='100%' xml:space='preserve' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                    <path d='M14.187,6.137c-0.104,0.081 -1.948,1.12 -1.948,3.429c0,2.672 2.346,3.617 2.416,3.64c-0.011,0.058 -0.372,1.294 -1.236,2.555c-0.771,1.108 -1.576,2.216 -2.8,2.216c-1.224,-0 -1.539,-0.711 -2.952,-0.711c-1.377,-0 -1.867,0.734 -2.987,0.734c-1.119,-0 -1.901,-1.026 -2.799,-2.286c-1.04,-1.48 -1.881,-3.779 -1.881,-5.961c0,-3.499 2.275,-5.355 4.515,-5.355c1.19,-0 2.182,0.781 2.929,0.781c0.711,0 1.82,-0.828 3.173,-0.828c0.514,0 2.357,0.047 3.57,1.786Zm-4.212,-3.268c0.56,-0.664 0.956,-1.585 0.956,-2.507c-0,-0.128 -0.011,-0.258 -0.035,-0.362c-0.91,0.034 -1.994,0.607 -2.648,1.365c-0.513,0.583 -0.991,1.504 -0.991,2.439c-0,0.14 0.023,0.281 0.034,0.326c0.057,0.01 0.151,0.023 0.245,0.023c0.817,0 1.845,-0.547 2.439,-1.284Z' style='fill-rule:nonzero;'></path>
                </svg>
                Sign in with Apple ID
            </button>
        </div>
        <span class="or-separator" aria-hidden="true">or</span>
        <div class="form-group">
            <label class="form-label" for="your-email">Email</label>
            <input class="form-control form-control--lg" id="your-email" type="email" name="your-email" required="required" />
        </div>
        <div class="form-group">
            <label class="form-label form-label--space-between" for="your-password">Password <a href="/password-reset/">Forgot your password?</a>
            </label>
            <input class="form-control form-control--lg" id="your-password" type="password" name="your-password" required="required" />
        </div>
        <div class="form-group">
            <label class="form-check form-check--lg" for="remember-me">
                <input class="form-check__control" id="remember-me" type="checkbox" name="remember-me" />
                <span class="form-label form-check__label">Remember me</span>
            </label>
        </div>
        <div class="form-group">
            <button class="btn btn--primary btn--lg btn--block btn--primary-shadow">
                Sign In
            </button>
        </div>
    </div>
</form>
@endsection

{{-- Footer --}}
@section('footer')
<p>Not a member? <a href="/sign-up/">Sign Up</a></p>
@endsection
