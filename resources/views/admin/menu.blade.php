@if(Auth::user()->hasAnyRole(['admin','admin2']))
    <li class="nav-item"><a class="nav-link" href="{{route('usersettings.index')}}"><i class="material-icons light-blue">settings_input_component</i> <span class="toggle-none">Invoice @lang('main.settings.menu')</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('sales.import.view')}}"><i class="material-icons blue">cloud_upload</i> <span class="toggle-none">Upload CSV</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('sales.index')}}"><i class="material-icons amber">work_outline</i> <span class="toggle-none">Uploaded CSV</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('invoices.mass.maker')}}" onclick="return confirm('By clicking okay all new invoices will be generate. This action may take some time to complete.');"><i class="material-icons purple">group_work</i> <span class="toggle-none">Mass Invoice Maker</span> </a></li>

    <li class="nav-item"><a class="nav-link" href="{{route('customers.index')}}"><i class="material-icons red">folder_shared</i> <span class="toggle-none">@lang('main.customers.menu')</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('invoices.index')}}"><i class="material-icons light-green">style</i> <span class="toggle-none">@lang('main.invoices.menu')</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('sales.index',['type'=>'job'])}}"><i class="material-icons blue">find_in_page</i> <span class="toggle-none">Job Search</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('invoices.index',['printer'=>2])}}"><i class="material-icons orange">offline_pin</i> <span class="toggle-none">Printed Invoices</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('invoices.index',['printer'=>1])}}"><i class="material-icons purple">description</i> <span class="toggle-none">Unprinted Invoices</span> </a></li>

    <li class="nav-item"><a class="nav-link" href="{{route('sales.index.export')}}"><i class="material-icons amber">input</i> <span class="toggle-none">Export CSV/SAGE</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('admincomposer.index')}}"><i class="material-icons light-blue">send</i> <span class="toggle-none">Mass Mail</span> </a></li>

    <li class="nav-heading"><span>NEW DATA ARCHIVES</span></li>
    <li class="nav-item"><a class="nav-link" href="{{route('archive-sales.index')}}"><i class="material-icons red">work_outline</i> <span class="toggle-none">Uploaded CSV (Archived)</span> </a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('archive-invoices.index')}}"><i class="material-icons red">style</i> <span class="toggle-none">@lang('main.invoices.menu') (Archived)</span> </a></li>

    <li class="nav-heading"><span>OLD DATABASE ARCHIVES</span></li>
    <li class="nav-item"><a class="nav-link" href="{{route('archive.sales.index')}}"><i class="material-icons red">work_outline</i> Archived Sales</a></li>
    <li class="nav-item"><a class="nav-link" href="{{route('archive.invoices.index')}}"><i class="material-icons red">offline_pin</i> <span class="toggle-none">Archived Invoices</span> </a></li>
    {{--<li class="nav-item"><a class="nav-link" href="{{route('archive.invoices.index',['printer'=>1])}}"><i class="material-icons red">description</i> <span class="toggle-none">Unprinted Invoices</span> </a></li>--}}
@else
{{--<li class="nav-item"><a class="nav-link" href="{{route('sales.index',['type'=>'job'])}}"><i class="material-icons blue">find_in_page</i> <span class="toggle-none">Job Search</span> </a></li>--}}
<li class="nav-item"><a class="nav-link" href="{{route('invoices.index')}}"><i class="material-icons light-green">style</i> <span class="toggle-none">@lang('main.invoices.menu')</span> </a></li>
<li class="nav-item"><a class="nav-link" href="{{route('archive.invoices.index')}}"><i class="material-icons red">offline_pin</i> <span class="toggle-none">Archived Invoices</span> </a></li>

@endif
