{{-- Start Register Modal --}}
<div class="modal fade modal-fade-in-scale-up modal-orange" id="RegModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document" style="overflow-y: initial !important;">
    <div class="modal-content">

      <div class="user-background" style="background: url({{asset('img/game_pattern_white.png')}});"></div>

      <div class="modal-header" >
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        <div class="title flex-center-space">
          {{-- Title (Create Account) --}}
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-user-plus" aria-hidden="true"></i>
            <strong> {{ trans('auth.create_account') }}</strong>
          </h4>
          <div>
            {{-- Sign in button --}}
            <a data-dismiss="modal" data-toggle="modal" href="#LoginModal" class="btn btn-success btn-round m-r-5 f-w-500"><i class="fa fa-sign-in" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('auth.login') }}</a></span>
            {{-- Modal close button --}}
            <a href="/#" data-dismiss="modal" class="btn btn-round btn-dark">
              <i class="fa fa-times" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="modal-body user-body">
        <div class="row no-space">
          <div class="col-md-6 form" id="register">
          {{ Form::open(['url' => 'register', 'id' => 'registerForm']) }}
          {{ csrf_field() }}
            <div class="bg-danger error reg" id="register-errors-name">
            </div>
            {{-- Username input --}}
            <div class="input-group m-b-10" id="register-name">
              <span class="input-group-addon login-form">
                <i class="fa fa-user" aria-hidden="true"></i>
              </span>
              <input id="register-name" type="input" class="form-control input rounded" name="name" placeholder="{{ trans('auth.username') }}">
            </div>
            <div class="bg-danger error reg" id="register-errors-email">
            </div>
            {{-- eMail Adress input --}}
            <div class="input-group m-b-10" id="register-email">
              <span class="input-group-addon login-form">
                <i class="fa fa-envelope" aria-hidden="true"></i>
              </span>
              <input id="register-email" type="email" class="form-control input rounded" name="email" placeholder="{{ trans('auth.email') }}">
            </div>
            <div class="bg-danger error reg" id="register-errors-password">
            </div>
            {{-- Password input --}}
            <div class="input-group m-b-10" id="register-password">
              <span class="input-group-addon login-form">
                <i class="fa fa-unlock-alt" aria-hidden="true"></i>
              </span>
              <input id="register-password" type="password" class="form-control input" name="password" placeholder="{{ trans('auth.password') }}">
            </div>
            {{-- Pasword confirmation input --}}
            <div class="input-group m-b-10" id="register-password-confirm">
              <span class="input-group-addon login-form">
                <i class="fa fa-repeat" aria-hidden="true"></i>
              </span>
              <input id="register-password-confirmation" type="password" class="form-control input" name="password_confirmation" placeholder="{{ trans('auth.password_confirmation') }}">
            </div>
            <div class="bg-danger error reg" id="register-errors-legal">
            </div>
            {{-- Legal checkbox --}}
            @if(config('settings.register_checkbox'))
              @php
                  // Get the terms of service page
                  $terms_service_page = \Cache::rememberForever('terms_service_page', function () {
                      return \App\Models\Page::find(config('settings.terms_service'));
                  });

                  // Get the privacy policy page
                  $privacy_policy_page =  \Cache::rememberForever('privacy_policy_page', function () {
                     return \App\Models\Page::find(config('settings.privacy_policy'));
                  });
              @endphp
              <div class="checkbox-custom checkbox-default m-b-10" id="register-legal">
                <input name="legal" id="register-legal-checkbox" type="checkbox" />
                <label for="register-legal-checkbox">
                  {{-- Checkbox for terms of service --}}
                  @if(config('settings.register_checkbox') == 'terms' && $terms_service_page)
                    {!! trans('auth.terms_checkbox', ['terms_link' => $terms_service_page->getPageLink(),'terms_name' => $terms_service_page->name]) !!}
                  {{-- Checkbox for privacy policy --}}
                  @elseif(config('settings.register_checkbox') == 'privacy' && $privacy_policy_page)
                    {!! trans('auth.privacy_checkbox', ['privacy_link' => $privacy_policy_page->getPageLink(),'privacy_name' => $privacy_policy_page->name]) !!}
                  {{-- Checkbox for terms of service and privacy policy --}}
                  @elseif(config('settings.register_checkbox') == 'terms_privacy' && $terms_service_page && $privacy_policy_page)
                    {!! trans('auth.terms_privacy_checkbox', ['terms_link' => $terms_service_page->getPageLink(),'terms_name' => $terms_service_page->name,'privacy_link' => $privacy_policy_page->getPageLink(),'privacy_name' => $privacy_policy_page->name]) !!}
                  @endif
                </label>
              </div>
            @endif
            {{-- Check if reCAPTCHA is enabled --}}
            @if(config('settings.recaptcha_register'))
              {{-- Register button (invisible reCaptcha) --}}
              {!! app('captcha')->display($attributes = ['data-theme'=>'dark'], trans('auth.create_account')); !!}
            @else
              {{-- Register button --}}
              <button type="submit" id="register-submit" class="btn btn-orange btn-block btn-animate btn-animate-vertical">
                <span><i class="icon fa fa-user-plus" aria-hidden="true"></i> {{ trans('auth.create_account') }}</span>
              </button>
            @endif
          {{ Form::close() }}
          </div>

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
              <a href="{{ url('login/facebook') }}" class="btn btn-tagged btn-block social-facebook f-w-500" rel="nofollow">
                <span class="btn-tag"><i class="icon fab fa-facebook-f" aria-hidden="true"></i></span> {{ trans('auth.login_facebook') }}
              </a>
              @endif
              {{-- Sign in with twitter --}}
              @if( config('settings.twitter_auth') )
              <a href="{{ url('login/twitter') }}" class="btn btn-tagged btn-block social-twitter f-w-500" rel="nofollow">
                <span class="btn-tag"><i class="icon fab fa-twitter" aria-hidden="true"></i></span> {{ trans('auth.login_twitter') }}
              </a>
              @endif
              {{-- Sign in with gogle+ --}}
              @if( config('settings.google_auth') )
              <a href="{{ url('login/google') }}" class="btn btn btn-tagged btn-block social-google-plus f-w-500" rel="nofollow">
                <span class="btn-tag"><i class="icon fab fa-google-plus-g" aria-hidden="true"></i></span> {{ trans('auth.login_google') }}
              </a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Register Modal --}}

@push('scripts')
  {{-- Start Register Form --}}
  <script>
    var registerForm = $("#registerForm");
    var registerSubmit = $("#register-submit");

    @if(config('settings.recaptcha_register'))
    {{-- execute google recaptcha --}}
    function reCaptcha(token) {
      grecaptcha.execute();
    }
    @endif

    function registerFormSubmit(token) {
      registerForm.submit();
    }

    registerForm.submit(function(e){
      e.preventDefault();
      var formData = registerForm.serialize();
      $( '#register-errors-name' ).html( "" );
      $( '#register-errors-email' ).html( "" );
      $( '#register-errors-password' ).html( "" );
      $( '#register-errors-name' ).slideUp('fast');
      $( '#register-errors-email' ).slideUp('fast');
      $( '#register-errors-password' ).slideUp('fast');
      $('#register-errors-legal').slideUp('fast');
      $('#register-name').removeClass('has-error');
      $('#register-email').removeClass('has-error');
      $('#register-password').removeClass('has-error');
      $('#register-password-confirm').removeClass('has-error');
      $('#register-legal').removeClass('has-error');

      $.ajax({
          url:'{{url('register')}}',
          type:'POST',
          data:formData,
          beforeSend: function(){
              registerSubmit.prop( "disabled", true ).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
          },
          success:function(data){
              $('#registerModal').modal( 'hide' );
              window.location.href=data;
          },
          error: function (data) {
             $('#register').shake({
               speed: 80
             });
             {{-- Reset google recaptcha --}}
             @if(config('settings.recaptcha_register'))
              grecaptcha.reset();
             @endif
             registerSubmit.prop( "disabled", false ).html('{{ trans('auth.create_account') }}');
             var obj = jQuery.parseJSON( data.responseText );
             if(obj.errors.name){
                $('#register-name').addClass('has-error');
                $('#register-errors-name').slideDown('fast');
                $('#register-errors-name').html( obj.errors.name );
              }
              if(obj.errors.email){
                $('#register-email').addClass('has-error');
                $('#register-errors-email').slideDown('fast');
                $('#register-errors-email').html( obj.errors.email );
              }
              if(obj.errors.password){
                $('#register-password').addClass('has-error');
                $('#register-password-confirm').addClass('has-error');
                $('#register-errors-password').slideDown('fast');
                $('#register-errors-password').html( obj.errors.password );
              }

              if(obj.errors.legal){
                $('#register-legal').addClass('has-error');
                $('#register-errors-legal').slideDown('fast');
                $('#register-errors-legal').html( obj.errors.legal );
              }
          }
      });
    });
  </script>
  {{-- End Register Form --}}
@endpush
