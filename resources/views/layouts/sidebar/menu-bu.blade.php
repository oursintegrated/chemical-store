<ul class="list-group">
    <li class="list-group-item {{ \Request::segment(1) == 'home' ? 'active' : '' }}">
        <a href="{{url('/')}}">
            <span class="fa fa-home" data-toggle="tooltip" title="Dashboard"></span>
            <span class="list-group-item-value">Dashboard <span class="badge">1</span> </span>
        </a>
    </li>
    <li class="list-group-item secondary-nav-item-pf {{ in_array(\Request::segment(1), ['store']) ? 'active' : '' }}" data-target="#master-data">
        <a>
            <span class="fa fa-bookmark" data-toggle="tooltip" title="Master Data"></span>
            <span class="list-group-item-value">Master Data</span>
        </a>

        <div id="master-data" class="nav-pf-secondary-nav">
            <div class="nav-item-pf-header">
                <a class="secondary-collapse-toggle-pf" data-toggle="collapse-secondary-nav"></a>
                <span>Master Data</span>
            </div>
        </div>
    </li>
    <li class="list-group-item secondary-nav-item-pf {{ in_array(\Request::segment(1), ['user', 'role', 'setting']) ? 'active' : '' }}" data-target="#configuration">
        <a>
            <span class="pficon pficon-settings" data-toggle="tooltip" title="Configuration"></span>
            <span class="list-group-item-value">Configuration</span>
        </a>

        <div id="configuration" class="nav-pf-secondary-nav">
            <div class="nav-item-pf-header">
                <a class="secondary-collapse-toggle-pf" data-toggle="collapse-secondary-nav"></a>
                <span>Configuration</span>
            </div>
            <ul class="list-group">
                <li class="list-group-item">
                    <a href="{{url('/user')}}">
                        <span class="list-group-item-value">User</span>
                    </a>
                </li>
                <li class="list-group-item">
                    <a href="{{url('/role')}}">
                        <span class="list-group-item-value">Role</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>