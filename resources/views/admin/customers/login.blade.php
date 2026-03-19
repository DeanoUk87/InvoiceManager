
@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.customers.title')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav justify-content-between">
                    <a class="navbar-brand">Allow Customer {{$customers->customer_account}} to Login</a>
                    <a href="{{route('customers.index')}}" class="btn btn-info btn-sm"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                <form action="{{route('customers.login.store')}}" method="post" id="hezecomform" name="hezecomform">
                    {{ csrf_field() }}

	                <div class="form-group">
	                     <input id="email" name="email" class="form-control styler" type="text" value="{{$customers->customer_email}}" />
	                </div>

                    <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                        <input id="username" type="text" class="form-control p_input" name="username" value="{{$customers->customer_account}}" readonly="readonly">
                        @if ($errors->has('username'))
                            <span class="help-block">
                              <strong>{{ $errors->first('username') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input id="password" type="password" class="form-control " name="password" placeholder="@lang('app.users.fields.password')" required>
                        @if ($errors->has('password'))
                            <span class="help-block">
                              <span>{{ $errors->first('password') }}</span>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <input id="password-confirm" type="password" class="form-control p_input" name="password_confirmation" placeholder="@lang('app.users.fields.password2')" required>
                    </div>



                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-update" id="btnStatus">
                           Grand Access
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
        
