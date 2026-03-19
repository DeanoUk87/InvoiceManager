@extends('layouts.form')
@section('content')
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('app.roles.edit')</a>
                    <a href="#" class="btn btn-info btn-sm" onclick="viewAll('{{route('roles.index')}}')"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                </nav>
            </div>

            <div class="card-body">

                <div class="hts-flash"></div>

                {{ Form::model($role, array('route' => array('roles.update', $role->id), 'method' => 'post','id'=>'hezecomform','class'=>'form-horizontal')) }}

                <input type="hidden" id="id" name="id" value="{{ $role->id }}">

                <div class="form-group">
                    {{ Form::label('name', 'Role Name') }}
                    {{ Form::text('name', null, array('class' => 'form-control')) }}
                </div>

                <p>@lang('app.roles.assign')</p>

                <div class='form-group scrollarea'>
                    @foreach ($permissions as $permission)
                        <div class='row'>
                            <div class='col-md-1'>
                                {{ Form::checkbox('permissions[]',  $permission->id, $role->permissions, ['class' => 'tgl tgl-ios','id' => $permission->id]) }}
                                <label class="tgl-btn" for="{{$permission->id}}"></label>
                            </div>
                            <div class='col-md-10' style="margin-left: 10px">
                                <p style="font-size: 14px; color: #999;">{{ucfirst($permission->name)}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btnStatus">
                        <span class="fa fa-hdd"></span> @lang('app.update.btn')
                    </button>
                </div>

                {{ Form::close() }}
            </div>

        </div>
    </div>
</div>
@endsection