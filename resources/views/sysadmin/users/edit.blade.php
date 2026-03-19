@extends('layouts.form')
@section('content')
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('app.users.update')</a>
                    <a href="#" class="btn btn-info" onclick="viewAll('{{route('users.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>
            <div class="card-body">
                <div class="hts-flash"></div>
                {{ Form::model($user, array('route' => array('users.update', $user->id), 'method' => 'post','id'=>'hezecomform','class'=>'form-horizontal')) }}
                <input type="hidden" id="id" name="id" value="{{ $user->id }}">
                <div class="form-group">
                    {{ Form::label('username', 'Username/Account No.') }}
                    {{ Form::text('username', null, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('email', 'Email') }}
                    {{ Form::email('email', null, array('class' => 'form-control')) }}
                </div>
                <h5>@lang('app.roles.title')</h5>
                <div class='form-group scrollarea'>
                    @foreach ($roles as $role)
                        <div class='row'>
                            <div class='col-md-1'>
                                {{ Form::checkbox('roles[]',  $role->id, $user->roles, ['class' => 'tgl tgl-ios','id' => $role->id]) }}
                                <label class="tgl-btn" for="{{$role->id}}"></label>
                            </div>
                            <div class='col-md-10' style="margin-left: 10px">
                                <p style="font-size: 14px; color: #999;">{{ucfirst($role->name)}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btnStatus">
                        <span class="fa fa-hdd"></span> @lang('app.change.btn')
                    </button>
                </div>

                {{ Form::close() }}

            </div>

        </div>
    </div>
</div>
@endsection