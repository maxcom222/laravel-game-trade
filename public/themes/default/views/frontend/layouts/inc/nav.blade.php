{{-- Progress bar for ajax loading --}}
<nav class="site-navbar navbar navbar-dark navbar-fixed-top navbar-inverse"
role="navigation" style="{{ (config('settings.landing_page') && !Auth::check() && Request::is('/') || Request::is('games/*') && !Request::is('games/add')) || Request::is('games') || Request::is('user/*') || Request::is('login') || Request::is('password/reset/*') || Request::is('offer/*') || Request::is('listings') || (Request::is('listings/*') && !Request::is('listings/add') && !Request::is('listings/*/add') && !Request::is('listings/*/edit') ) ? 'background: linear-gradient(0deg, rgba(34,33,33,0) 0%, rgba(34,33,33,0.8) 100%);' : 'background-color: rgba(34,33,33,1);' }} -webkit-transition: all .3s ease 0s; -o-transition: all .3s ease 0s; transition: all .3s ease 0s; z-index: 20;">

  {{-- Start header --}}
  <div class="navbar-header">

    {{-- Toggle offcanvas navigation (Mobile only) --}}
    <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided navbar-toggle offcanvas-toggle"
   data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas" id="offcanvas-toggle">
      <span class="sr-only">{{ trans('general.nav.toggle_nav') }}</span>
      <span class="hamburger-bar"></span>
    </button>

    {{-- Toggle sub navigation (Mobile only) --}}
    <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
    data-toggle="collapse">
      <i class="icon fa fa-ellipsis-h" aria-hidden="true"></i>
    </button>
    {{-- Logo --}}
    <a class="navbar-brand navbar-brand-center" href="{{ url('') }}">
      <img src="{{ asset(config('settings.logo')) }}"
      title="Logo" class="hires">
    </a>

  </div>
  {{-- End header --}}

  {{-- Start Navigation --}}
  <div class="navigation">

    <div class="navbar-container navbar-offcanvas navbar-offcanvas-touch" id="js-bootstrap-offcanvas" style="margin-left: 0px; margin-right: 0px; padding-left: 0px; padding-right: 0px;">

      <ul class="site-menu" data-plugin="menu">
        {{-- Close button (only offcanvas menu) --}}
        <li class="site-menu-item hidden-md-up">
          <a href="javascript:void(0)" data-toggle="offcanvas" data-target="#js-bootstrap-offcanvas" id="offcanvas-toggle" class="offcanvas-toggle">
            <i class="site-menu-icon fa fa-times" aria-hidden="true"></i>
            <span class="site-menu-title">{{ trans('general.close') }}</span>
          </a>
        </li>
        {{-- Start listings nav --}}
        <li class="site-menu-item has-sub {{ Request::is('listings/*') || ( URL::current() == url('listings') ) ? 'active': '' }}">
          <a href="javascript:void(0)" data-toggle="dropdown">
            <span class="site-menu-title"><i class="site-menu-icon fa fa-tags" aria-hidden="true"></i> {{ trans('general.listings') }}</span><span class="site-menu-arrow"></span>
          </a>
          <div class="dropdown-menu  site-menu-games">
            <div class="row no-space site-menu-sub">
              {{-- Start Current Generation Nav --}}
              <div class="col-xs-12 col-md-4" style="border-right: 1px solid rgba(255,255,255,0.05);">
                <div class="site-menu-games-title">{{ trans('general.nav.current_generation') }}</div>
                <ul>
                  <li class="site-menu-item menu-item-game {{ ( URL::current() == url('listings') ) ? 'active' : null }}">
                    <a href="{{ url('listings')}}">
                      <span class="site-menu-title site-menu-fix">{{ trans('listings.general.all_listings') }}</span>
                    </a>
                  </li>
                  <li class="site-menu-item menu-item-game {{ ( URL::current() == url('listings/ps4') ) ? 'active' : null }}">
                    <a href="{{ url('listings/ps4')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation 4</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/xboxone') ) ? 'active' : null }}">
                    <a href="{{ url('listings/xboxone')}}">
                      <span class="site-menu-title site-menu-fix">Xbox One</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/pc') ) ? 'active' : null }}">
                    <a href="{{ url('listings/pc')}}">
                      <span class="site-menu-title site-menu-fix">PC</span>
                    </a>
                  </li>
                  <li class="site-menu-item menu-item-game {{ ( URL::current() == url('listings/switch') ) ? 'active' : null }}">
                    <a href="{{ url('listings/switch')}}">
                      <span class="site-menu-title site-menu-fix">Nintendo Switch</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/wii-u') ) ? 'active' : null }}">
                    <a href="{{ url('listings/wii-u')}}">
                      <span class="site-menu-title site-menu-fix">Wii U</span>
                    </a>
                  </li>
                </ul>
              </div>
              {{-- End Current Generation Nav --}}
              {{-- Start last generation nav --}}
              <div class="col-xs-12 col-md-4" style="border-right: 1px solid rgba(255,255,255,0.05);">
                <div class="site-menu-games-title">{{ trans('general.nav.last_generation') }}</div>
                <ul style="padding: 0; list-style-type: none;">
                  <li class="site-menu-item {{ ( URL::current() == url('listings/ps3') ) ? 'active' : null }}">
                    <a href="{{ url('listings/ps3')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation 3</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/xbox360') ) ? 'active' : null }}">
                    <a href="{{ url('listings/xbox360')}}">
                      <span class="site-menu-title site-menu-fix">Xbox 360</span>
                    </a>
                  </li>
                </ul>
                {{-- End last generation nav --}}
                {{-- Start handhelds nav --}}
                <div class="site-menu-games-title">{{ trans('general.nav.handhelds') }}</div>
                <ul>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/3ds') ) ? 'active' : null }}">
                    <a href="{{ url('listings/3ds')}}">
                      <span class="site-menu-title site-menu-fix">Nintendo 3DS</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/vita') ) ? 'active' : null }}">
                    <a href="{{ url('listings/vita')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation Vita</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/ds') ) ? 'active' : null }}">
                    <a href="{{ url('listings/ds')}}">
                      <span class="site-menu-title site-menu-fix">Nintendo DS</span>
                    </a>
                  </li>
                </ul>
                {{-- End handhelds nav --}}
              </div>
              {{-- Start retro nav --}}
              <div class="col-xs-12 col-md-4">
                <div class="site-menu-games-title">{{ trans('general.nav.retro') }}</div>
                <ul>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/ps2') ) ? 'active' : null }}">
                    <a href="{{ url('listings/ps2')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation 2</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/xbox') ) ? 'active' : null }}">
                    <a href="{{ url('listings/xbox')}}">
                      <span class="site-menu-title site-menu-fix">Xbox</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/ps') ) ? 'active' : null }}">
                    <a href="{{ url('listings/ps')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/wii') ) ? 'active' : null }}">
                    <a href="{{ url('listings/wii')}}">
                      <span class="site-menu-title site-menu-fix">Wii</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/gamecube') ) ? 'active' : null }}">
                    <a href="{{ url('listings/gamecube')}}">
                      <span class="site-menu-title site-menu-fix">Gamecube</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/n64') ) ? 'active' : null }}">
                    <a href="{{ url('listings/n64')}}">
                      <span class="site-menu-title site-menu-fix">Nintendo 64</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/gba') ) ? 'active' : null }}">
                    <a href="{{ url('listings/gba')}}">
                      <span class="site-menu-title site-menu-fix">Game Boy Advance</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/psp') ) ? 'active' : null }}">
                    <a href="{{ url('listings/psp')}}">
                      <span class="site-menu-title site-menu-fix">PlayStation Portable</span>
                    </a>
                  </li>
                  <li class="site-menu-item {{ ( URL::current() == url('listings/dreamcast') ) ? 'active' : null }}">
                    <a href="{{ url('listings/dreamcast')}}">
                      <span class="site-menu-title site-menu-fix">Dreamcast</span>
                    </a>
                  </li>
                </ul>
              </div>
              {{-- End retro nav --}}
            </div>
          </div>
        </li>
        {{-- End listings nav --}}
        {{-- Games navbar --}}
        <li class="site-menu-item {{ Request::is('games/*') || ( URL::current() == url('games') ) ? 'active': '' }}">
          <a href="{{ url('games')}}">
            <span class="site-menu-title"><i class="site-menu-icon fa fa-gamepad" aria-hidden="true"></i> {{ trans('general.games') }}</span>
          </a>
        </li>

        {{-- Search navbar --}}
        <li class="site-menu-item">
          <a href="javascript:void(0)" data-toggle="collapse" data-target="#site-navbar-search" role="button" id="navbar-search-open">
            <i class="site-menu-icon fa fa-search hidden-sm-down" aria-hidden="true"></i>
            <span class="site-menu-title hidden-md-down">{{ trans('general.nav.search') }}</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="navbar-container container-fluid userbar">
      <!-- Navbar Collapse -->
      <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <!-- Navbar Toolbar -->

        {{-- Start Search toggle for mobile view --}}
        <button type="button" class="navbar-toggler collapsed float-left" data-target="#site-navbar-search"
        data-toggle="collapse">
          <span class="sr-only">{{ trans('general.nav.toggle_search') }}</span>
          <i class="icon fa fa-search" aria-hidden="true"></i>
        </button>
        {{-- End Search toggle for mobile view --}}

        <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
          {{-- Start User nav --}}
          @if(Auth::check())
            <li class="nav-item dropdown">
              <a class="nav-link" href="{{ url('messages') }}" title="Messages" role="button" >
                <i class="fas @if(!Request::is('messages') && $unreadMessagesCount>0) fa-envelope-open @else fa-envelope @endif"></i>
                {{-- Count unread notifications --}}
                @if(!Request::is('messages') && $unreadMessagesCount>0)
                  <span id="unread-messages" class="badge badge-danger badge-sm up">{{$unreadMessagesCount}}</span>
                @endif
              </a>
            </li>


          <li class="nav-item dropdown" id="dropdown-notifications">
            <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)" title="Notifications" role="button" >
              <i class="icon fa fa-bell @if(count(Auth::user()->unreadNotifications)>0) faa-shake animated @endif" aria-hidden="true"></i>
              {{-- Count unread notifications --}}
              @if(count(Auth::user()->unreadNotifications)>0)
                <span class="badge badge-danger badge-sm up">{{count(Auth::user()->unreadNotifications)}}</span>
              @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-nofications">
              <li class="dropdown-notifications-loading">
                <i class="fa fa-refresh fa-spin fa-2x" aria-hidden="true"></i>
              </li>
              <li class="dropdown-notifications-content">
              </li>
              {{-- Subscribe to push notifications --}}
              @if(config('settings.onesignal'))
              <li class="dropdown-notifications-push-subscribe" id="subscribe-push" style="display:none;">
                <a href="#" id="subscribe-push-link">
                  <i class="fa fa-dot-circle-o" aria-hidden="true"></i>
              {{ trans('general.nav.user.notifications_push_subscribe') }}
                </a>
              </li>
              @endif
              {{-- Show all notifications --}}
              <li class="dropdown-notifications-showall">
                <a href="{{ url('dash/notifications')}}">
                  <i class="fa fa-bell"></i> {{ trans('general.nav.user.notifications_all') }}
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-item dropdown">
            <a class="nav-link navbar-avatar flex-center" data-toggle="dropdown" href="#" aria-expanded="false"
            data-animation="scale-up" role="button">

              <span class="avatar avatar-online">
                {{-- <img src="{{ access()->user()->picture }}"  border=0 width=75/> --}}
                <img src="{{Auth::user()->avatar_square_tiny}}" alt="{{Auth::user()->name}}" border="0" width="75"><i></i>
                <i></i>
              </span>
              <span class="m-l-5 m-r-10"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
            </a>
            <div class="dropdown-menu" role="menu" style="">
              @can('access_backend')
              <a class="dropdown-item" href="{{url('admin')}}" role="menuitem"><i class="icon fa fa-id-badge" aria-hidden="true"></i> {{ trans('general.nav.user.admin') }}</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              @endcan
              @if(config('settings.payment'))
              <a class="dropdown-item" href="{{url('dash/balance')}}" role="menuitem"><i class="icon far fa-money-bill" aria-hidden="true"></i> <strong>{{ money(abs(filter_var(number_format( Auth::user()->balance,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</strong></a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              @endif
              <a class="dropdown-item" href="{{url('dash')}}" role="menuitem"><i class="icon fa fa-tachometer" aria-hidden="true"></i> {{ trans('general.nav.user.dashboard') }}</a>
              <a class="dropdown-item" href="{{url('dash/listings')}}" role="menuitem"><i class="icon fa fa-tags" aria-hidden="true"></i> {{ trans('general.nav.user.listings') }}</a>
              <a class="dropdown-item" href="{{url('dash/offers')}}" role="menuitem"><i class="icon fa fa-briefcase" aria-hidden="true"></i> {{ trans('general.nav.user.offers') }}</a>
              <a class="dropdown-item" href="{{url('dash/wishlist')}}" role="menuitem"><i class="icon fa fa-heart" aria-hidden="true"></i> {{ trans('wishlist.wishlist') }}</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              <a class="dropdown-item" href="{{ url('dash/notifications') }}" role="menuitem"><i class="icon fa fa-bell" aria-hidden="true"></i> {{ trans('general.nav.user.notifications') }}</a>
              <a class="dropdown-item" href="{{ url('dash/settings') }}" role="menuitem"><i class="icon fa fa-wrench" aria-hidden="true"></i> {{ trans('general.nav.user.settings') }}</a>
              <a class="dropdown-item" href="{{Auth::user()->url}}" role="menuitem"><i class="icon fa fa-user" aria-hidden="true"></i> {{ trans('general.nav.user.profile') }}</a>
              <div class="dropdown-divider" role="presentation" style="opacity:0.1;"></div>
              <a class="dropdown-item" href="{{url('logout')}}" role="menuitem"><i class="icon fa fa-power-off" aria-hidden="true"></i> {{ trans('general.nav.user.logout') }}</a>
            </div>
          </li>

          @endif

          @if(Auth::check())
          {{-- Add Listing Button --}}
          <a href="{{url('listings/add')}}" aria-expanded="false" role="button" class="btn btn-orange btn-round navbar-btn navbar-right" style="font-weight: 500;">
            <i class="fa fa-plus"></i><span class="hidden-md-down"> {{ trans('general.nav.listing_add') }}</span>
          </a>
          @endif

          @if(!Auth::check())
          {{-- Sign Up Button --}}
          <a data-toggle="modal" data-target="#RegModal" href="javascript:void(0)" aria-expanded="false" role="button" class="btn btn-orange btn-round navbar-btn navbar-right" style="font-weight: 500; border-radius: 0px 50px 50px 0px;">
            <i class="fa fa-user-plus"></i>
          </a>
          {{-- Sign in Button --}}
          <a data-toggle="modal" data-target="#LoginModal" href="javascript:void(0)" aria-expanded="false" role="button" class="btn btn-success btn-round navbar-btn navbar-right" style="font-weight: 500; border-radius: 50px 0px 0px 50px">
            <i class="fa fa-sign-in"></i> {{ trans('auth.login') }}
          </a>
          @endif
          {{-- End User nav --}}
        </ul>
      </div>
      <!-- End Navbar Collapse -->
    </div>

    <!-- Site Navbar Seach -->
    <div class="collapse navbar-search-overlap" id="site-navbar-search" style="width: 100%;">
      <form role="search" id="search">
        <div class="form-group">
          <div class="input-search input-search-fix">
            <i class="input-search-icon fa fa-search" aria-hidden="true" id="loadingcomplete"></i>
            <i class="input-search-icon fa fa-sync fa-spin" aria-hidden="true" id="loadingsearch" style="display: none; margin-top: -8px !important;"></i>
            <input type="text" class="form-control" name="input" placeholder="{{ trans('general.nav.search') }}" id="navbar-search" autocomplete="off">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="button" class="input-search-close icon fa fa-times" data-target="#site-navbar-search"
            data-toggle="collapse" aria-label="Close" id="search-close"></button>
          </div>
        </div>
      </form>
    </div>
    <!-- End Site Navbar Seach -->
  </div>
  {{-- End Navigation --}}
</nav>
