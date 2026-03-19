@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.admincomposer.title')
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
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">Edit Message</a>
                        <a href="{{route('admincomposer.index')}}" class="btn btn-info btn-sm"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                    </nav>
                </div>
                <div class="card-body">
                    <div class="hts-flash"></div>
                    <form action="{{route('admincomposer.update',['id'=>$admincomposer->id])}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{$admincomposer->id}}">

                        <div class="form-group">
                            <label class="control-label" for="title">@lang('main.admincomposer.field.title')</label>
                            <input id="title" name="title" class="form-control styler" type="text" maxlength="200"  value="{{$admincomposer->title}}" />
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-heading card-default">
                                        @lang('main.admincomposer.field.message')
                                    </div>
                                    <div class="card-block editor-fit">
                                        <textarea class="editor1" name="message" >{!! $admincomposer->message !!}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-update" id="btnStatus">
                                @lang('app.update.btn')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
        
