{{-- Start Login Modal --}}
<div class="modal fade modal-fade-in-scale-up modal-success" id="LoginModal" tabindex="-1" role="dialog">
  <div class="modal-dialog user-dialog" role="document">
    <div class="modal-content">

      <div class="user-background" style="background: url({{asset('img/game_pattern_white.png')}});"></div>

      <div class="modal-header" >
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        <div class="title flex-center-space">
          {{-- Modal Title (Icon - Sign In) --}}
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-sign-in" aria-hidden="true"></i><strong> {{ trans('auth.login') }}</strong>
          </h4>
          {{-- Sign up button next to modal title (Icon - Create Account) --}}
          <div>
            <a data-dismiss="modal" data-toggle="modal" href="#RegModal" class="btn btn-warning btn-round m-r-5 f-w-500"><i class="fa fa-user-plus" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('auth.create_account') }}</span></a>
            {{-- Modal close button --}}
            <a href="/#" data-dismiss="modal" class="btn btn-round btn-dark">
              <i class="fa fa-times" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="modal-body user-body">
        <div class="row no-space">
          <div class="col-md-6 social">
            <div class="logo">
              <img src="{{ asset(config('settings.logo')) }}" class="hires" />
            </div>
            <div class="buttons">
              {{-- Sign in with twitch --}}
              @if( config('settings.twitch_auth') )
              <a href="{{ url('login/twitch') }}" class="btn btn-tagged btn-block social-twitch f-w-500">
                <span class="btn-tag"><i class="icon fab fa-twitch" aria-hidden="true"></i></span> {{ trans('auth.login_twitch') }}
              </a>
              @endif
              {{-- Sign in with steam --}}
              @if( config('settings.steam_auth') )
              <a href="{{ url('login/steam') }}" class="btn btn-tagged btn-block social-steam f-w-500">
                <span class="btn-tag"><i class="icon fab fa-steam" aria-hidden="true"></i></span> {{ trans('auth.login_steam') }}
              </a>
              @endif
              {{-- Sign in with facebook --}}
              @if( config('settings.facebook_auth') )
              <a href="{{ url('login/facebook') }}" class="btn btn-tagged btn-block social-facebook f-w-500">
                <span class="btn-tag"><i class="icon fab fa-facebook" aria-hidden="true"></i></span> {{ trans('auth.login_facebook') }}
              </a>
              @endif
              {{-- Sign in with twitter --}}
              @if( config('settings.twitter_auth') )
              <a href="{{ url('login/twitter') }}" class="btn btn-tagged btn-block social-twitter f-w-500">
                <span class="btn-tag"><i class="icon fab fa-twitter" aria-hidden="true"></i></span> {{ trans('auth.login_twitter') }}
              </a>
              @endif
              {{-- Sign in with gogle+ --}}
              @if( config('settings.google_auth') )
              <a href="{{ url('login/google') }}" class="btn btn btn-tagged btn-block social-google-plus f-w-500">
                <span class="btn-tag"><i class="icon fab fa-google-plus-g" aria-hidden="true"></i></span> {{ trans('auth.login_google') }}
              </a>
              @endif
            </div>
          </div>

          <div class="col-md-6 form" id="loginform">
            {{-- Login failed msg --}}
            <div class="bg-danger error" id="loginfailed">
              <i class="fa fa-times" aria-hidden="true"></i> {{ trans('auth.failed') }}
            </div>

            <form id="loginForm" method="POST" novalidate="novalidate">
              {{ csrf_field() }}
              {{-- eMail Adress input --}}
              <div class="input-group m-b-10">
                <span class="input-group-addon login-form">
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
                <input id="email" type="email" class="form-control input rounded" name="email" value="{{ old('email') }}" placeholder="{{ trans('auth.email') }}">
              </div>
              {{-- Password input --}}
              <div class="input-group">
                <span class="input-group-addon login-form">
                  <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                </span>
                <input id="password" type="password" class="form-control input" name="password" placeholder="{{ trans('auth.password') }}">
              </div>
              {{-- Remember me --}}
              <div class="checkbox-custom checkbox-default">
                <input name="remember" id="remember" type="checkbox" />
                <label for="remember">{{ trans('auth.remember_me') }}</label>
              </div>
              {{-- Login button --}}
              <button type="submit" class="btn btn-success btn-block btn-animate btn-animate-vertical" id="login">
                <span><i class="icon fa fa-sign-in" aria-hidden="true"></i> {{ trans('auth.login') }}</span>
              </button>
              {{-- Forget password --}}
              <a data-dismiss="modal" data-toggle="modal" href="#ForgetModal" class="btn btn-dark btn-block">{{ trans('auth.password_forgot') }}</a>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Login Modal --}}
