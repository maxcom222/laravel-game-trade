@extends(Theme::getLayout())

@section('subheader')
{{-- Gif Background --}}
<div class="page-login-gif-bg"></div>
{{-- Color overlay --}}
<div class="page-login-color-bg"></div>
@stop

@section('content')
  {{-- Start page login --}}
  <div class="page-login">
    <div class="vertical-align text-center">
      <div class="page-content vertical-align-middle">
        @if (session()->has('error'))
          <div class="panel border-radius bg-danger m-b-10 p-10">
            <i class="fa fa-times"></i> {!! session('error') !!}
          </div>
        @endif
        @if (session()->has('success'))
          <div class="panel border-radius bg-success m-b-10 p-10">
            <i class="fa fa-check"></i> {!! session('success') !!}
          </div>
        @endif
        <div class="panel" id="panelshake">
          <div class="game-bg"></div>
          <div class="panel-body padding-40">
            {{-- Logo --}}
            <div class="brand">
              <img src="{{ asset(config('settings.logo')) }}"
              title="Logo" class="hires">
            </div>
            {{-- Top Text --}}
            <h3>{{ trans('auth.login') }}</h3>
            {{-- Start social buttons --}}
            <div class="m-t-20">
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
                  <span class="btn-tag"><i class="icon fab fa-facebook-f" aria-hidden="true"></i></span> {{ trans('auth.login_facebook') }}
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
            {{-- End social buttons --}}

            {{-- Strike --}}
            <div class="strike m-t-10 m-b-10">
              <span>{{ trans('auth.login_or') }}</span>
            </div>

            {{-- Login failed msg --}}
            <div class="bg-danger error" id="loginfailedFull">
              <i class="fa fa-times" aria-hidden="true"></i> {{ trans('auth.failed') }}
            </div>
            {{-- Start Login form --}}
            <form method="post" id="loginFormFull">
              {{-- eMail Adress input --}}
              <div class="input-group m-b-10">
                <span class="input-group-addon login-form">
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
                <input id="email-form" type="email" class="form-control input rounded" name="email" value="{{ old('email') }}" placeholder="{{ trans('auth.email') }}">
              </div>
              {{-- Password input --}}
              <div class="input-group m-b-10">
                <span class="input-group-addon login-form">
                  <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                </span>
                <input id="password-form" type="password" class="form-control input" name="password" placeholder="{{ trans('auth.password') }}">
              </div>
              {{-- Remember me checkbox --}}
              <div class="form-group flex-center-space">
                <div class="checkbox-custom checkbox-default checkbox-inline">
                  <input name="remember" id="remember_full" type="checkbox" />
                  <label for="remember_full">{{ trans('auth.remember_me') }}</label>
                </div>
                {{-- Reset password --}}
                <div>
                  <a data-dismiss="modal" data-toggle="modal" href="#ForgetModal">{{ trans('auth.password_forgot') }}</a>
                </div>
              </div>
              {{-- Login button --}}
              <button type="submit" class="btn btn-success btn-block btn-animate btn-animate-vertical" id="loginFull">
                <span><i class="icon fa fa-sign-in" aria-hidden="true"></i> {{ trans('auth.login') }}</span>
              </button>
            </form>
            {{-- End Login form --}}
          </div>
          {{-- Create account --}}
          <div class="panel-login-footer">
            <div class="create-account">
              {{ trans('auth.no_account_question') }} <a data-toggle="modal" data-target="#RegModal" href="javascript:void(0)">{{ trans('auth.no_account_question_create') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- End page login --}}

  @section('after-scripts')
    <script type="text/javascript">
    (function($) {
    	$.fn.shake = function(o) {
    		if (typeof o === 'function')
    			o = {callback: o};
    		// Set options
    		var o = $.extend({
    			direction: "left",
    			distance: 20,
    			times: 3,
    			speed: 140,
    			easing: "swing"
    		}, o);

    		return this.each(function() {

    			// Create element
    			var el = $(this), props = {
    				position: el.css("position"),
    				top: el.css("top"),
    				bottom: el.css("bottom"),
    				left: el.css("left"),
    				right: el.css("right")
    			};

    			el.css("position", "relative");

    			// Adjust
    			var ref = (o.direction == "up" || o.direction == "down") ? "top" : "left";
    			var motion = (o.direction == "up" || o.direction == "left") ? "pos" : "neg";

    			// Animation
    			var animation = {}, animation1 = {}, animation2 = {};
    			animation[ref] = (motion == "pos" ? "-=" : "+=")  + o.distance;
    			animation1[ref] = (motion == "pos" ? "+=" : "-=")  + o.distance * 2;
    			animation2[ref] = (motion == "pos" ? "-=" : "+=")  + o.distance * 2;

    			// Animate
    			el.animate(animation, o.speed, o.easing);
    			for (var i = 1; i < o.times; i++) { // Shakes
    				el.animate(animation1, o.speed, o.easing).animate(animation2, o.speed, o.easing);
    			};
    			el.animate(animation1, o.speed, o.easing).
    			animate(animation, o.speed / 2, o.easing, function(){ // Last shake
    				el.css(props); // Restore
    				if(o.callback) o.callback.apply(this, arguments); // Callback
    			});
    		});
    	};
    })(jQuery);

    $(document).ready(function(){

      var loginForm = $("#loginFormFull");
      loginForm.submit(function(e){
          e.preventDefault();
          var formData = loginForm.serialize();

          $.ajax({
              url:'{{ url("login") }}',
              type:'POST',
              data:formData,
              {{-- Send CSRF Token over ajax --}}
              headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
              beforeSend: function(){
                  $("#loginfailedFull").slideUp();
                  $("#loginFull").prop( "disabled", true );
                  $("#loginFull").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
              },
              success:function(data){
                  $("#panelshake").shake({
                    direction: "up",
                    speed: 80
                  });
                  window.location.href=data;
              },
              error: function (data) {
                  $("#panelshake").shake({
                    speed: 80
                  });
                  $("#loginfailedFull").slideDown();
                  $("#loginFull").prop( "disabled", false );
                  $("#loginFull").html('Login');
              }
          });
      });

    });
    </script>
  @endsection
@stop
