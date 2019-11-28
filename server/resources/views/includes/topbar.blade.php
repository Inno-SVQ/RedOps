<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false">
                        <img src="{{ Gravatar::src(Auth::user()->email) }}" alt="Avatar of {{ Auth::user()->name }}">
                        {{ Auth::user()->name }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li><a href="javascript:;"> Profile</a></li>
                        <li>
                            <a href="javascript:;">
                                <span class="badge bg-red pull-right">50%</span>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li><a href="javascript:;">Help</a></li>
                        <li><a href="{{ url('/logout') }}"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                </li>

                <li role="presentation" class="dropdown">
                    @if(isset($selectedAudit))
                        <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown"
                           aria-expanded="false">
                            <i id="i-current-jobs" class="fa fa-inbox fa-3x fa-fw margin-bottom"></i>
                            <span id="badge-current-jobs" class="badge bg-green"></span>
                        </a>
                    @endif
                    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
                        @foreach(Auth::user()->jobNotifications() as $notification)
                            <li>
                                <a>
                                <span class="image">
                                    <img src="{{ Gravatar::src(Auth::user()->email) }}" alt="Profile Image"/></span>
                                    <span>
                                    <span>{{$notification->title}}</span>
                                    <span class="time">{{$notification->getHumanTime()}}</span>
                                </span>
                                    <span class="message">{{$notification->content}}</span>
                                </a>
                            </li>
                        @endforeach
                        <li>
                            <div class="text-center">
                                <a>
                                    <strong>See All Alerts</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->
