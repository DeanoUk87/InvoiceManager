<div class="main-sidebar-nav light-navigation">
    <div class="nano">
        <div class="nano-content sidebar-nav">
            <ul class="metisMenu nav flex-column" id="menu">
                <div class="card-block border-bottom text-center nav-profile">
                </div>
                <li class="nav-heading"><span>@lang('app.system_menu')</span></li>
                <li class="nav-item"><a class="nav-link" href="{{url('home')}}"><i class="material-icons blue">dashboard</i></i> <span class="toggle-none">@lang('app.dashboard') </span></a></li>
                {{--@if(Auth::user()->hasRole('admin'))--}}
                @role('admin')
                <li class="nav-item">
                    <a class="nav-link" href="javascript: void(0);" aria-expanded="true"><i class="material-icons purple">account_box</i> <span class="toggle-none">@lang('app.account')<span class="fa arrow"></span></span></a>
                    <ul class="nav-second-level nav flex-column sub-menu" aria-expanded="true">
                        <li class="nav-item"><a class="nav-link" href="{{route('users.index')}}">@lang('app.users.title')</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('roles.index')}}">@lang('app.roles.title')</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{route('permissions.index')}}">@lang('app.permissions.title')</a></li>
                    </ul>
                </li>
                {{--<li class="nav-item"><a class="nav-link" href="{{route('settings.index')}}"><i class="material-icons green">settings</i> <span class="toggle-none">App @lang('app.settings')</span> </a></li>--}}
                @endrole
                {{--Include Generated Codes--}}
                @includeIf('admin.menu')
            </ul>
        </div>
    </div>
</div>