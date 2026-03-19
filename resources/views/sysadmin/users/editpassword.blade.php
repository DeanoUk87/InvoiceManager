@extends('layouts.form')
@section('content')
    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">@lang('app.users.reset_password')</a>
                        <a href="#" class="btn btn-info" onclick="viewAll('{{route('users.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                    </nav>
                </div>
                <div class="card-body">
                    <div class="hts-flash"></div>
                    {{ Form::model($user, array('route' => array('users.updatepassword', $user->id), 'method' => 'post','id'=>'hezecomform','class'=>'form-horizontal')) }}
                    <input type="hidden" id="id" name="id" value="{{ $user->id }}">
                    <div class="form-group">
                        <input id="passwor-confirm" type="password" class="form-control" name="password" placeholder="@lang('app.users.fields.password')">
                    </div>
                    <div class="form-group">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="@lang('app.users.fields.password2')">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btn-save">
                            <span class="fa fa-hdd"></span> @lang('app.change.btn')
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection