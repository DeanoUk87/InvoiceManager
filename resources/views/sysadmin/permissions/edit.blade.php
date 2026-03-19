@extends('layouts.form')
@section('content')
<div class="row mb-2 htsDisplay">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header" style="">
                <nav class="nav  justify-content-between">
                    <a class="navbar-brand">@lang('app.edit') {{$permission->name}}</a>
                    <a href="#" class="btn btn-info" onclick="viewAll('{{route('permissions.index')}}')"><i class="fa fa-undo-alt"></i> @lang('app.goback')</a>
                </nav>
            </div>

            <div class="card-body">

                <div class="hts-flash"></div>

                {{ Form::model($permission, array('route' => array('permissions.update', $permission->id), 'method' => 'post','id'=>'hezecomform')) }}

                <input type="hidden" id="id" name="id" value="{{ $permission->id }}">

                <div class="form-group">
                    {{ Form::label('name', 'Permission Name') }}
                    {{ Form::text('name', null, array('class' => 'form-control')) }}
                </div>
                <div class="form-group">
                    {{ Form::label('route', 'Route - (the route you want to protect)') }}
                    {{ Form::text('route', null, array('class' => 'form-control','placeholder' => 'e.g admin/posts/create')) }}
                </div>
                <br>
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