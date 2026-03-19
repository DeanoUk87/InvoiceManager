@extends('layouts.form')
@section('content')
    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">@lang('app.permissions.create')</a>
                        <a href="#" class="btn btn-info" onclick="viewAll('{{route('permissions.index')}}')"><i class="fa fa-undo-alt"></i> @lang('app.goback')</a>
                    </nav>
                </div>

                <div class="card-body">
                    <div class="hts-flash"></div>
                    <form action="{{route('permissions.store')}}" method="post" id="hezecomform" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group">
                            {{ Form::label('name', 'Permission Name') }}
                            {{ Form::text('name', '', array('class' => 'form-control','placeholder' => 'e.g posts_create')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('route', 'Route - (the route you want to protect)') }}
                            {{ Form::text('route', '', array('class' => 'form-control','placeholder' => 'e.g admin/posts/create')) }}
                        </div>
                        <br>
                        @if(!$roles->isEmpty())
                            {{-- <h4>Assign Permission to Roles</h4>
                             @foreach ($roles as $role)
                                 {{ Form::checkbox('roles[]',  $role->id ) }}
                                 {{ Form::label($role->name, ucfirst($role->name)) }}<br>
                             @endforeach--}}
                            <p>@lang('app.permissions.assign')</p>
                            <div class='form-group scrollarea'>
                                @foreach ($roles as $role)
                                    <div class='row'>
                                        <div class='col-md-1'>
                                            <input class="tgl tgl-ios" name="roles[]" id="{{$role->id}}" value="{{$role->id}}" type="checkbox"/>
                                            <label class="tgl-btn" for="{{$role->id}}"></label>
                                        </div>
                                        <div class='col-md-10' style="margin-left: 10px">
                                            <p style="font-size: 14px; color: #999;">{{ucfirst($role->name)}}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btnStatus">
                                <span class="fa fa-plus"></span> @lang('app.add.btn')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection