<!DOCTYPE html>
<html class="no-js css-menubar{{ !Request::is('games') && !Request::is('listings') ? ' overflow-smooth' : '' }}{{ Request::is('messages') ? ' messages' : '' }}" lang="{{config('settings.default_locale')}}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    {{-- Meta --}}
    @if(config('settings.facebook_client_id') != '')
    <meta property="fb:app_id" content="{{config('settings.facebook_client_id')}}" />
    @endif
    {{-- Add unread notification count in title --}}
    @if(Auth::check())
      @php $unreadMessagesCount = Auth::user()->unreadMessagesCount(); @endphp
    @endif
    {{-- Check if user is logged in and if user have unread notifications --}}
    @if(Auth::check() && (count(Auth::user()->unreadNotifications)>0 || $unreadMessagesCount>0))
      @php
      // Get current SEO title
      $title = SEO::getTitle();
      // Append unread notifications count to meta title
      SEOMeta::setTitle('(' . ((int)count(Auth::user()->unreadNotifications)  + (!Request::is('messages') ? (int)$unreadMessagesCount : 0)) . ') ' . $title);
      @endphp
    @endif
    {{-- Generate SEO tags --}}
    {!! SEO::generate() !!}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('img/safari-pinned-tab.svg') }}" color="#302f2f">
    {{-- Sitemap --}}
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ url('sitemap') }}" />
    {{-- OpenSearch --}}
    <link href="{{ url('opensearch.xml') }}" rel="search" title="{{ config('settings.page_name') }} {{ trans('general.nav.search') }}" type="application/opensearchdescription+xml">

    <meta name="theme-color" content="#302f2f">
    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('themes/' . Theme::getCurrent() . '/assets/css/site.css') }}?version=1.4.2">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/notie/notie.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/owlcarousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/owlcarousel/assets/owl.theme.default.min.css') }}">

    {{-- Fonts --}}
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    {{-- Additional css from settings --}}
    @if(config('settings.css'))
    <style>
      {!! config('settings.css') !!}
    </style>
    @endif
    {{-- Laravel csrfToken for Ajax form headers --}}
    @php
        echo "<script>window.Laravel = " . json_encode(['csrfToken' => csrf_token(),]) . "</script>";
    @endphp
  </head>

  <body class="site-navbar-small">
    {{-- Navigation --}}
    @include('frontend.layouts.inc.nav')
    {{-- Start Page--}}
    {{-- Subheader --}}
    @yield('subheader')
    @yield('content-full-width')
    @hasSection('content')
    <div class="page {{ Request::is('listings/*') || Request::is('games/*') ? 'game-overview' : '' }}">
      <div class="page-content container-fluid" >
        {{-- Content --}}
        @yield('content')
      </div>
    </div>
    @endif
    {{-- Breadcrumbs --}}
    @yield('breadcrumbs')
    {{-- End Page --}}
    {{-- Footer --}}
    @if(!Request::is('messages'))
      @include('frontend.layouts.inc.footer')
    @endif
    {{-- Auth modals --}}
    @if(!Auth::check())
      @include('frontend.auth.inc.login_modal')
      @include('frontend.auth.inc.forget_password_modal')
      @include('frontend.auth.inc.register_modal')
    @endif
  </body>
  {{-- Google Searchbox --}}
  <script type="application/ld+json">
  {
    "@context": "http://schema.org",
    "@type": "WebSite",
    "name": "{{ config('settings.page_name') }}",
    "url": "{{ url('/') }}",
    "potentialAction": [{
      "@type": "SearchAction",
      "target": "{{ url('search/') }}/{search_term_string}",
      "query-input": "required name=search_term_string"
    }]
  }
  </script>
  {{-- Scripts --}}
  @yield('before-scripts')
  {{-- Vendor JS --}}
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/tether.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script async src="{{ asset('js/bootstrap.offcanvas.js') }}"></script>
  <script async src="{{ asset('js/velocity.min.js') }}"></script>
  <script src="{{ asset('js/typeahead.bundle.min.js') }}"></script>
  <script src="{{ asset('js/jquery.lazyload.min.js') }}"></script>
  {{-- Site JS --}}
  <script async src="{{ asset('js/site.js') }}"></script>
  <script src="{{ asset('vendor/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js"></script>
  {{-- Additional JS from admin panel --}}
  {!! config('settings.js') !!}
  @yield('after-scripts')
  @stack('scripts')
  {{-- OneSignal Push Notifications --}}
  @if(Auth::check() && config('settings.onesignal'))
  <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async='async'></script>
  <script>
    {{-- Init OneSignal --}}
    var OneSignal = window.OneSignal || [];
    OneSignal.push(["init", {
      appId: '{{ config('services.onesignal.app_id') }}',
      {{-- Safari Web ID --}}
      @if(config('settings.onesignal_safari_web_id'))
      safari_web_id: '{{ config('settings.onesignal_safari_web_id') }}',
      @endif
      autoRegister: true,
      welcomeNotification: {
          "title": "{{ config('settings.page_name') }}",
          "message": "{{ trans('notifications.push.welcome') }}",
          // "url": ""
      },
      notifyButton: {
        enable: false
      }
    }]);

    {{-- Subscribe function --}}
    function subscribe() {
      OneSignal.push(["registerForPushNotifications"]);
      event.preventDefault();
    }

    OneSignal.push(function() {
      {{-- Check if browser support push notifications --}}
      if (!OneSignal.isPushNotificationsSupported()) {
         return;
      }

      {{-- Show subscribe link, check if user is already subscribed--}}
      OneSignal.isPushNotificationsEnabled(function(isEnabled) {
        if (!isEnabled) {
          document.getElementById("subscribe-push-link").addEventListener('click', subscribe);
          document.getElementById("subscribe-push").style.display = '';
        }
      });

      {{-- Get player id from user --}}
      OneSignal.getUserId(function(playerId) {
          OneSignal.on('subscriptionChange', function (isSubscribed) {
            {{-- User subscribed - save player id to database --}}
            if (isSubscribed) {
                {{-- Get new player id after subscribe --}}
                $("#subscribe-push").hide();
                OneSignal.getUserId(function(playerId) {
                    $.ajax({
                        type:'POST',
                        url:'{{ url('user/push/add') }}',
                        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                        data: {
                            'player_id': playerId
                        }
                    });
                });
            {{-- User unsubscribed - remove player id from database --}}
            } else {
                $.ajax({
                    type:'POST',
                    url:'{{ url('user/push/remove') }}',
                    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
                    data: {
                        'player_id': playerId
                    }
                });
            }
          });
      });



    });
  </script>
  @endif

  <script src="{{ asset('js/notie.min.js') }}"></script>
  {{-- Bootstrap Notifications using notie Alerts --}}
  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $('.col-xs-6').matchHeight();

      @foreach (Alert::getMessages() as $type => $messages)
          @foreach ($messages as $message)
            notie.alert('{{ $type }}', '{!! $message !!}',5)
          @endforeach
      @endforeach
    });
  </script>

  @if(!Auth::check())

  <script>

  @if(config('settings.locate_position'))
  {{-- Save geolocation --}}
  function saveLocation(position) {
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
      $.ajax({
          type:'POST',
          url:'{{ url('geolocation/save') }}',
          headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
          data: {
              'latitude': latitude,
              'longitude': longitude
          }
      });
  }
  @endif

  $(document).ready(function(){

    @if(config('settings.locate_position') && !session()->has('latitude') && !session()->has('longitude'))
    {{-- Get current position --}}
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(saveLocation);
    }
    @endif

    {{-- Login Form --}}
    var loginForm = $("#loginForm");
    var loginSubmit = $("#login");
    loginForm.submit(function(e){
        e.preventDefault();
        var formData = loginForm.serialize();

        $.ajax({
            url:'{{ url("login") }}',
            type:'POST',
            data:formData,
            beforeSend: function(){
                $("#loginfailed").slideUp();
                loginSubmit.prop( "disabled", true ).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            },
            success:function(data){
                $("#loginform").shake({
                  direction: "up",
                  speed: 80
                });
                window.location.href=data;
            },
            error: function (data) {
                $("#loginform").shake({
                  speed: 80
                });
                $("#loginfailed").slideDown();
                loginSubmit.prop( "disabled", false ).html('{{ trans('auth.login') }}');
            }
        });
    });
    {{-- Forget Password Form --}}
    var forgetForm = $("#forgetForm");
    forgetForm.submit(function(e){
        e.preventDefault();
        var formData = forgetForm.serialize();
        $('#forget-errors-email').html('');
        $('#forget-errors-email').slideUp('fast');
        $('#forget-email').removeClass('has-error');
        $.ajax({
            url:'{{url('password/email')}}',
            type:'POST',
            data:formData,
            success:function(data){
              $('#forgetForm').slideUp('fast');
              $('#forget-success').slideDown('fast');
            },
            error: function (data) {
              console.log(data.responseText);
              var obj = jQuery.parseJSON( data.responseText );
              if(obj.email){
                $('#forget-email').addClass('has-error');
                $('#forget-errors-email').slideDown('fast');
                $('#forget-errors-email').html( obj.email );
              }
            }
        });
    });
  });
  </script>
  @endif

  {{-- Start navbar typeahead search --}}
  <script type="text/javascript">
  $(document).ready(function(){
    @if(Auth::check())
    {{-- Load notifications on dropdown click --}}
    $('#dropdown-notifications').on('show.bs.dropdown', function () {
      if($('.dropdown-notifications-content' ).children().length == 0){
        $.ajax({
            url:'{{ url("dash/notifications/api") }}',
            type:'GET',
            success:function(data){
              $('.dropdown-notifications-loading').fadeOut('fast', function() {
                $('.dropdown-notifications-content' ).hide().html(data).fadeIn('fast');
              });
            },
            error: function (data) {
              alert('Oops, an error occurred!')
            }
        });
      }
    })
    @endif

    @if((config('settings.landing_page') && !Auth::check() && Request::is('/') || Request::is('games/*') && !Request::is('games/add') ) || Request::is('games') || Request::is('user/*') || Request::is('login') || Request::is('password/reset/*') || Request::is('offer/*') || Request::is('listings') || (Request::is('listings/*') && !Request::is('listings/add') && !Request::is('listings/*/add') && !Request::is('listings/*/edit') ))
    {{-- Scroll function for navbar --}}
    var scroll = function () {
      if(lastScrollTop >= 30){
        $('.site-navbar').css('background-color','rgba(34,33,33,1)');
        $(".sticky-header").removeClass('slide-up')
        $(".sticky-header").addClass('slide-down')
      }else{
        $('.site-navbar').css('background','linear-gradient(0deg, rgba(34,33,33,0) 0%, rgba(34,33,33,0.8) 100%)');
      }
    };
    var raf = window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        window.oRequestAnimationFrame;
    var $window = $(window);
    var lastScrollTop = $window.scrollTop();

    if (raf) {
        loop();
    }

    function loop() {
        var scrollTop = $window.scrollTop();
        if (lastScrollTop === scrollTop) {
            raf(loop);
            return;
        } else {
            lastScrollTop = scrollTop;

            // fire scroll function if scrolls vertically
            scroll();
            raf(loop);
        }
    }
    @endif

    {{-- Focus search input on collapse --}}
    $(document).on('click', '[data-toggle="collapse"]', function(e) {
      $('#navbar-search').focus();
    });
    {{-- Redirect to search results when user click enter button --}}
    $('#navbar-search').keypress(function(e) {
      if(e.which == 13){
        e.preventDefault();
        if($('#navbar-search').val() != "")
          window.location.href = {!! '"' . url('/search/') . '/"'!!} + $('#navbar-search').val();
      }
    });
    {{-- Bloodhound engine with remote search data in json format --}}
    var gameSearch = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.whitespace,
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      sorter: false,
      remote: {
        url: '{{ url("games/search/json/%QUERY") }}',
        wildcard: '%QUERY'
      }
    });
    {{-- Typeahead with data from bloodhound engine --}}
    $('#navbar-search').typeahead(null, {
      name: 'navbar-search',
      display: 'name',
      source: gameSearch,
      highlight: true,
      limit:6,
      templates: {
        empty: [
          '<div class="nosearchresult bg-danger" >',
            '<span><i class="fa fa-ban"></i> {{ trans('general.nav.search_empty') }}<span>',
          '</div>'
        ].join('\n'),
        suggestion: function (data) {
            var price;
            if(data.cheapest_listing != '0') {
              cheapest_listing = '<span class="price"> {{ trans('general.nav.starting_from')}} <strong>' + data.cheapest_listing + '</strong></span>';
            }else{
              cheapest_listing = '';
            }

            if(data.listings != '0') {
              listings = '<span class="listings-label"><i class="fa fa-tags"></i> ' + data.listings + '</span>';
            }else{
              listings = '';
            }
            return '<div class="searchresult navbar"><a href="' + data.url + '"><div class="inline-block m-r-10"><span class="avatar"><img src="' + data.pic + '" class="img-circle"></span></div><div class="inline-block"><strong class="title">' + data.name + '</strong><span class="release-year m-l-5">' + data.release_year +'</span><br><small class="text-uc text-xs"><span class="platform-label" style="background-color: ' + data.platform_color + ';">' + data.platform_name + '</span> ' + listings + ''+ cheapest_listing +'</small></div></a></div>';
        }
      }
    })
    .on('typeahead:asyncrequest', function() {
        $('.input-search').removeClass('input-search-fix');
        $('#loadingcomplete').hide();
        $('#loadingsearch').show();
    })
    .on('typeahead:asynccancel typeahead:asyncreceive', function() {
        $('#loadingsearch').hide();
        $('#loadingcomplete').show();
    });
    {{-- Reset input and add search-fix class on closing --}}
    $( "#search-close" ).click(function() {
        $('.input-search').addClass('input-search-fix');
        $('#navbar-search').typeahead('val', '');
    });
  })
  </script>
  {{-- End navbar typeahead search --}}

  @if(!Auth::check())
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
  </script>
  @endif
</html>
