@extends('layouts.auth')

@section('title')
    @lang('app.header_title') | @lang('app.users.reset_password')
@endsection

@section('content')

<div class="container-scroller">
    <div class="container-fluid">
        <div class="row">
            <div class="content-wrapper full-page-wrapper d-flex align-items-center auth-pages">
                <div class="card col-lg-4 mx-auto">
                    <div class="card-body">
                        <h3 class="card-title text-left mb-3">@lang('app.users.reset_password')</h3>
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <input id="email" type="email" class="form-control p_input" name="email" placeholder="@lang('app.users.fields.email')" value="{{ old('email') }}" required>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <span>{{ $errors->first('email') }}</span>
                                    </span>
                                    @endif
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block enter-btn">@lang('app.users.reset_btn')</button>
                            </div>

                            <p class="existing-user text-center pt-4 mb-0">&nbsp;<a href="{{route('login')}}">@lang('app.users.sign_in')</a></p>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
