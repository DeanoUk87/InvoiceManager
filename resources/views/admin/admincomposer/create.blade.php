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
                        <a class="navbar-brand">Compose Message to Customers</a>
                        <a href="{{route('admincomposer.index')}}" class="btn btn-info btn-sm"><i class="fa fa-reply"></i> @lang('app.goback')</a>
                    </nav>
                </div>
                <div class="card-body">
                    <div class="hts-flash"></div>
                    <form action="{{route('admincomposer.store')}}" method="post" id="hezecomform" name="hezecomform" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="to" value="Customers" />

                        <div class="form-group">
                            <label class="control-label" for="title">Title</label>
                            <input id="title" name="title" class="form-control styler" type="text" maxlength="200"  placeholder="Message Title" />
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-heading card-default">
                                        Message Body
                                    </div>
                                    <div class="card-block editor-fit">
                                        <textarea class="editor1" name="message" ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="document"></label><br>
                            <input type="file" class="form-control-file btn btn-lg btn-default" id="document" name="document">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-lg mr-2" name="btn-save" id="btnStatus">
                                Build Message
                            </button>
                            <div class="hts-flash"></div>
                            <small>This might take some time. Please wait until you get a successful message.</small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts');
<script type="text/javascript">
    $('#to').on('change',function(){
        var valStr = this.value;
        var valArray = valStr.split("|");
        if(valArray[0]==="User"){
            $("#gatewayType").show();
        }else{
            $("#gatewayType").hide();
        }
    });
</script>
@endsection
        
