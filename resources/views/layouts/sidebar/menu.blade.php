<ul class="list-group" id="sidebar-menu">
    @if(isset($menu->root))
    @foreach ($menu->root as $key => $value)
    <li class="list-group-item @if($value->child > 0) secondary-nav-item-pf @endif {{ in_array(\Request::segment(1), [strtolower($value->name)]) ? 'active' : '' }}" data-target="#master-data">
        <a @if($value->child == 0) href="/{{str_replace(' ', '', strtolower($value->name))}}" @endif>
            @if(strtolower($value->name) == 'dashboard')
            <span class="fa fa-home"></span>
            @elseif(strtolower($value->name) == 'data master')
            <span class="fa fa-bookmark"></span>
            @elseif(strtolower($value->name) == 'configuration')
            <span class="pficon pficon-settings"></span>
            @elseif(strtolower($value->name) == 'sales')
            <span class="pficon pficon-orders"></span>
            @else
            <span class="fa fa-file"></span>
            @endif
            <span class="list-group-item-value">{{$value->name}} @if(strtolower($value->name)=='dashboard') <span class="badge">1</span> @endif </span>
        </a>

        <div class="nav-pf-secondary-nav">
            <div class="nav-item-pf-header">
                <a class="secondary-collapse-toggle-pf" data-toggle="collapse-secondary-nav"></a>
                <span>{{$value->name}}</span>
            </div>

            @foreach($menu->sub as $keySub => $valueSub)
            @if($keySub == $key)
            @for ($i=0; $i<count($valueSub); $i++) <!-- Sub Menu -->
                <ul class="list-group">
                    <li class="list-group-item {{ in_array(\Request::segment(2), [str_replace(' ', '-', strtolower($valueSub[$i]->name))]) ? 'active' : '' }}">
                        <a href="{{url('/'.str_replace(' ', '-', strtolower($value->name)).'/'.str_replace(' ', '-', strtolower($valueSub[$i]->name)))}}">
                            <span class="list-group-item-value">{{$valueSub[$i]->name}}</span>
                        </a>
                    </li>
                </ul>
                @endfor
                @endif
                @endforeach
        </div>
    </li>
    @endforeach
    @endif
</ul>