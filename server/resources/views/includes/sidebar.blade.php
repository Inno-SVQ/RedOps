<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ url('/') }}" class="site_title"><i class="fa fa-fire"></i> <span>RedOps</span></a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile">
            <div class="profile_pic">
                <img src="{{ Gravatar::src(Auth::user()->email) }}" alt="Avatar of {{ Auth::user()->name }}"
                     class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2>{{ Auth::user()->name }}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Menu</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{route('home')}}">
                            <i class="fa fa-laptop"></i>
                            Dashboard
                        </a>
                    </li>
                    <li><a href="{{route('audits')}}"><i
                                    class="fa fa-home {{Request::path() == 'audits/*' ? 'current-page': ''}}"></i>
                            Audits </a></li>
                </ul>
            </div>
            <div class="menu_section">
                @if(isset($selectedAudit))
                    <h3>{{$selectedAudit->name}}</h3>
                    <ul class="nav side-menu">
                        <li><a href="{{route('jobs', $selectedAudit->id)}}"><i class="fa fa-sitemap"></i> Jobs <span class="label label-default">{{count($selectedAudit->jobs())}}</span></a></li>
                        <li>
                            <a><i class="fa fa-dot-circle-o"></i> Enumeration <span class="fa fa-chevron-down"></span></a>
                            <ul class="nav child_menu">
                                <li>
                                    <a href="{{route('companies', $selectedAudit->id)}}">Companies <span class="label label-default">{{count($selectedAudit->companies())}}</span></a>
                                </li>
                                <li>
                                    <a href="{{route('domains', $selectedAudit->id)}}">Hosts <span class="label label-default">{{count($selectedAudit->domains())}}</span></a>
                                </li>
                                <li>
                                    <a href="{{route('services', $selectedAudit->id)}}">Services <span class="label label-default">{{count($selectedAudit->services())}}</span></a>
                                </li>
                                <li>
                                    <a href="{{route('domains', $selectedAudit->id)}}">Webs <span class="label label-default">{{count($selectedAudit->services())}}</span></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endif
            </div>

        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ url('/logout') }}">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
        <!-- /menu footer buttons -->
    </div>
</div>