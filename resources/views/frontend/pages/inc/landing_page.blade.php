<div class="landing flex-center-space" style="height: calc(100vh - var(--vh-offset, 0px)) !important;">
  {{-- Landing background image - only visible if set in admin panel --}}
  @if(config('settings.landing_image'))
    <div class="landing-bg" style="background: url('{{ asset(config('settings.landing_image')) }}')"></div>
  @endif

  {{-- Background gradient --}}
  <div class="landing-gradient-bg"></div>

  {{-- Landing page content wrapper --}}
  <div class="landing-wrapper text-center">
    {{-- Flip text --}}
    <div class="flip-text">
      {{-- Text before flip text --}}
      <span class="text">{{ trans('general.landing.before_flip') }}</span>
      {{-- Flip text --}}
      <span class="flip">{{ trans('general.landing.flip') }}</span>
      {{-- Text after flip text --}}
      <span class="text">{{ trans('general.landing.after_flip') }}</span>
    </div>

    {{-- Game search --}}
    <div class="m-t-20 search">
      {{-- Gamepad icon --}}
      <i class="fa fa-gamepad landing-icon" aria-hidden="true" id="landing-complete"></i>
      {{-- Loading icon --}}
      <i class="fa fa-sync fa-spin landing-icon" aria-hidden="true" id="landing-searching" style="display: none;"></i>
      {{-- Search input --}}
      <input type="text" id="landing-search" placeholder="{{ trans('general.landing.search_placeholder') }}">
    </div>

    </div>

    {{-- Promoted game --}}
    @if(config('settings.landing_game'))
      @php
        // Get the landing game
        $landing_game =  \Cache::rememberForever('landing_game', function () {
           return \App\Models\Game::find(config('settings.landing_game'));
        });
      @endphp
      {{-- Check if game id exist --}}
      @if(isset($landing_game))
        {{-- Game button with game name --}}
        <div class="landing-game">
            <a class="game-button" href="{{ $landing_game->url_slug }}">{{ $landing_game->name }}</a>
        </div>
      @endif
    @endif
</div>


@push('scripts')
<script src="{{ asset('js/vh-check.min.js') }}"></script>
{{-- Load text flip plugin --}}
<link rel="stylesheet" href="{{ asset('vendor/simple-text-rotator/simpletextrotator.css') }}">
<script src="{{ asset('vendor/simple-text-rotator/jquery.simple-text-rotator.min.js') }}"></script>
<script>
$(document).ready(function(){
  {{-- vh mobile fix --}}
  vhCheck();
  {{-- Flip text options --}}
  $(".flip").textrotator({
    animation: "flipUp",
    separator: ",",
    speed: 2000
  });

  {{-- Bloodhound engine with remote search data in json format --}}
  var landingSearch = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    sorter: false,
    remote: {
      url: '{{ url("games/search/json/%QUERY") }}',
      wildcard: '%QUERY'
    }
  });
  {{-- Typeahead with data from bloodhound engine --}}
  $('#landing-search').typeahead(null, {
    name: 'landing-search',
    display: 'name',
    source: landingSearch,
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
      $('#landing-complete').hide();
      $('#landing-searching').show();
  })
  .on('typeahead:asynccancel typeahead:asyncreceive', function() {
      $('#landing-searching').hide();
      $('#landing-complete').show();
  });


});
</script>
@endpush
