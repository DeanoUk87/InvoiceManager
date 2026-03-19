@extends('layouts.auth')



@section('title')

    @lang('app.header_title') | @lang('app.users.sign_in')

@endsection



@section('content')

    <div class="container-scroller">

        <div class="container-fluid">

            <div class="row">

                <div class="content-wrapper full-page-wrapper d-flex align-items-center auth-pages">

                    <div class="card col-lg-4 mx-auto">

                        <div class="card-body">

							<p style="text-align: center"><img src="https://ap.im.dnweb.uk/APC-Overnight/apc-logo.png" width="85" height="85" alt=""/></p>

                            <h3 class="card-title text-left mb-3">@lang('app.users.sign_in')</h3>

                            <form class="form-horizontal" method="POST" action="{{ route('login') }}">

                                {{ csrf_field() }}



                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                    <input id="email" type="text" class="form-control p_input" name="email" value="{{ old('email') }}" placeholder="Username or Account Number" required autofocus>

                                    @if ($errors->has('email'))

                                        <span class="help-block">

                                            <strong>{{ $errors->first('email') }}</strong>

                                        </span>

                                    @endif

                                </div>



                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">

                                    <input id="password" type="password" class="form-control p_input" name="password" placeholder="@lang('app.users.fields.password')" required>

                                    @if ($errors->has('password'))

                                        <span class="help-block">

                                            <strong>{{ $errors->first('password') }}</strong>

                                        </span>

                                    @endif

                                </div>





                                <div class="form-group d-flex align-items-center justify-content-between">

                                    <div class="form-check">

                                        <label>

                                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('app.users.fields.remember_me')

                                        </label>

                                    </div>

                                    <a href="{{ route('password.request') }}" class="forgot-pass">@lang('app.users.forgot')</a>

                                </div>



                                <div class="text-center">

                                    <button type="submit" class="btn btn-primary btn-block enter-btn">@lang('app.users.sign_in')</button>

                                </div>



                                @if(env('FACEBOOK_AUTH')=='ON' or env('TWITTER_AUTH')=='ON' or env('GOOGLE_AUTH')=='ON')

                                <p class="Or-login-with my-3">@lang('app.users.login_alt')</p>

                                @endif

                                @if(env('FACEBOOK_AUTH')=='ON' and env('FACEBOOK_ID'))

                                <a href="{{ url('/auth/facebook') }}" class="facebook-login btn btn-facebook btn-block">@lang('app.users.facebook')</a>

                                @endif

                                @if(env('TWITTER_AUTH')=='ON' and env('TWITTER_ID'))

                                <a href="{{ url('/auth/twitter') }}" class="twitter-login btn btn-twitter btn-block">@lang('app.users.twitter')</a>

                                @endif

                                @if(env('GOOGLE_AUTH')=='ON' and env('GOOGLE_ID'))

                                <a href="{{ url('/auth/google') }}" class="google-login btn btn-google btn-block">@lang('app.users.google')</a>

                                @endif

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection