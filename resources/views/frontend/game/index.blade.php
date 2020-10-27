@extends(Theme::getLayout())

{{-- Subheader --}}
@section('subheader')
  <div style="position: relative">
    <div class="page-top-background">
      <div class="background-overlay listings-overview"></div>
    </div>
  </div>
@stop

{{-- Content --}}
@section('content')

  {{-- Load Google AdSense --}}
  @if(config('settings.google_adsense'))
    @include('frontend.ads.google')
  @endif

  {{-- Game title --}}
  <div class="panels-title border-bottom flex-center-space">
    {{-- Title --}}
    <div>
      <i class="fa fa-gamepad" aria-hidden="true"></i> {{ trans('games.overview.all_games') }}
    </div>
    {{-- Current page + page count --}}
    <div class="o-50">
      {{-- First check if pages exist --}}
      @if($games->lastPage())
      <span id="current-page">{{ $games->currentPage() }}</span> / <span id="last-page">{{ $games->lastPage() }}</span>
      @endif
    </div>
  </div>

  {{-- Start Games wrapper --}}
  <div id="games-wrapper">
    {{-- Start Filter / Sort options --}}
    <div class="m-b-20 flex-center-space">
        {{-- Start Filter button --}}
        <div>
            {{-- Filter Button with active filter count - open modal --}}
            <a href="#" data-toggle="modal" data-target="#modal_filter" class="btn btn-dark">
                <i class="fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }} @if(session()->get('listingsPlatformFilter') || session()->has('listingsOptionFilter')) ({{ ( session()->has('listingsPlatformFilter') ? count(session()->get('listingsPlatformFilter')) : 0) + ( session()->has('listingsOptionFilter') ? count(session()->get('listingsOptionFilter')) : 0)}}) @endif
            </a>
            {{-- Remove button - only visible with active filters --}}
            @if(session()->has('listingsPlatformFilter') || session()->has('listingsOptionFilter'))
            <a id="remove-filter" href="{{ url('listings/filter/remove') }}" class="m-l-5 btn btn-dark">
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
            @endif
        </div>
        {{-- End Filter button --}}
        {{-- Start sort options --}}
        <div>
            {{-- Sort order button (desc / asc) --}}
            <a id="order-direction" href="{{ url('games/order') }}/{{ session()->has('gamesOrder') ? session()->get('gamesOrder') : 'release_date' }}/{{  session()->has('gamesOrderByDesc') ? (session()->get('gamesOrderByDesc') ? 'asc' : 'desc') : 'asc' }}" class="btn btn-dark" style="vertical-align: inherit;">
                <i class="fa fa-sort-amount-{{ session()->has('gamesOrderByDesc') ? (session()->get('gamesOrderByDesc') ? 'up' : 'down') : 'up' }}" aria-hidden="true"></i>
            </a>
            {{-- Sort dropdown --}}
            <div class="m-l-5 inline-block">
                <select id="order_by" class="form-control select" style="height: 33px !important;">
                    {{-- Sort by --}}
                    <option disabled>{{ trans('general.sortfilter.sort_by') }}</option>
                    {{-- Release option --}}
                    <option value="{{ url('games/order/release_date') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'created_at' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_release') }}</option>
                    {{-- Metascore option --}}
                    <option value="{{ url('games/order/metascore') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'metascore' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_metascore') }}</option>
                    {{-- Listings option --}}
                    <option value="{{ url('games/order/listings') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'listings' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_listings') }}</option>
                    {{-- Popularity option --}}
                    <option value="{{ url('games/order/popularity') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'popularity' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_popularity') }}</option>
                </select>
            </div>
        </div>
        {{-- End sort options --}}
    </div>
    {{-- End Filter / Sort options --}}


    {{-- START GAME LIST --}}
    <div class="row">
      @forelse ($games as $game)
        @include('frontend.game.inc.card')
      @empty
        {{-- Start empty list message --}}
        <div class="empty-list">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
            {{ trans('games.overview.no_games') }}
          </div>
        </div>
        {{-- End empty list message --}}
      @endforelse


    </div>
    {{-- END GAME LIST --}}

    {{ $games->links() }}
  </div>
  {{-- End Games Wrapper --}}

  {{-- Start modal for filter options --}}
  <div class="modal fade modal-fade-in-scale-up modal-dark" id="modal_filter" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">

          <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

          <div class="title">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
            </button>
            {{-- Title (Filter) --}}
            <h4 class="modal-title" id="myModalLabel">
              <i class="fa fa-filter" aria-hidden="true"></i>
              {{ trans('general.sortfilter.filter') }}
            </h4>
          </div>

        </div>
        {{-- Start platform filters --}}
        <div class="modal-seperator">
            {{ trans('general.sortfilter.filter_platforms') }}
        </div>
        <div class="modal-body">
            @php
                // Get all platforms
                $platforms = Cache::rememberForever('platforms', function () {
                    return DB::table('platforms')->get();
                });
                // Active filters
                $active_filters = session()->get('listingsPlatformFilter') ?  session()->get('listingsPlatformFilter') : [];
            @endphp
            @foreach($platforms as $platform)
                {{-- Platform label --}}
                <a href="#" class="label platform-label platform-filter m-r-5 m-b-5 inline-block {{ in_array($platform->id, $active_filters) ? 'platform-filter-active' : '' }}" data-id="{{$platform->id}}" data-color="{{$platform->color}}" @if(in_array($platform->id, $active_filters)) style="background-color:{{$platform->color}};" @endif>
                    {{ $platform->name }}
                </a>
            @endforeach

        </div>
        {{-- End platform filters --}}
        <div class="modal-footer">
          {{-- Cancel button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-dark btn-animate btn-animate-vertical">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
          </a>
          {{-- Filter submit button --}}
          <a class="btn btn-success btn-animate btn-animate-vertical" id="filter-submit" href="#">
            <span>
              <i class="icon fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }}
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
  {{-- End modal for filter options --}}


  {{-- Start Breadcrumbs --}}
  @section('breadcrumbs')
  {!! Breadcrumbs::render('games') !!}
  @endsection
  {{-- End Breadcrumbs --}}

  {{-- Loading bar for AJAX Loading --}}
  <div class="load-progress"></div>
  <div class="load-progress-animation"></div>

  @section('after-scripts')
  <script type="text/javascript">
  $(document).ready(function(){
    {{-- AJAX Pagination --}}
    $(".pagination a").click(function(e) {
      e.preventDefault();
      {{-- Add spinner icon to the pagination link --}}
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw" style="margin-right: -3px; margin-left: -5px;"></i>');
      {{-- Get URL from link --}}
      var url = $(this).attr('href');
      ajaxLoad(url);
    });

    function ajaxLoad(url, callback) {
      {{-- Load URL through AJAX --}}
      $.ajax({
        {{-- Set load progress bar width to 10% before load for smoother animation --}}
        beforeSend: function () {
          $('.load-progress-animation').removeClass('hide');
          $('.load-progress').css({
            width:'10%'
          });
        },
        {{-- Update progress bar width during loading --}}
        xhr: function () {
          var xhr = new window.XMLHttpRequest();
          {{-- Event listener for loading the URL --}}
          xhr.addEventListener("progress", function (evt) {
            if (evt.lengthComputable) {
              {{-- Get percantage of complete loading --}}
              var percentComplete = evt.loaded / evt.total;
              {{-- Add the complete loading to the loading bar CSS --}}
              $('.load-progress').css({
                width: percentComplete * 100 + '%'
              });
              {{-- Remove loading bar if URL loaded --}}
              if (percentComplete === 1) {
                $('html, body').scrollTop(0);
                $('.load-progress-animation').addClass('hide');
                $('.load-progress').css({
                  width: '0%'
                });
              }
            }
          }, false);
          return xhr;
        },
        url: url,
        success: function(data) {
          {{-- Reset progress bar if XHR is not supported --}}
          $('html, body').scrollTop(0);
          $('.load-progress').css({
            width:  '100%'
          });
          $('.load-progress-animation').addClass('hide');
          $('.load-progress').css({
            width: '0%'
          });
          {{-- Change HTML with newly loaded HTML --}}
          $('#games-wrapper').html(data);
          {{-- Reset loading bar after hide animation (1.2s) --}}
          if (typeof callback === "function") {
            callback();
          }
        }
      });
    }


    {{-- Platform filter --}}
    $('.platform-filter').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('platform-filter-active')
        if ($(this).hasClass('platform-filter-active')) {
            $(this).css('background-color', $(this).data('color') );
        } else {
            $(this).css('background-color', '');
        }
    });

    {{-- Submit filter options --}}
    $('#filter-submit').click(function(e) {
        e.preventDefault();
        $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
        $(this).addClass('loading');
        {{-- Collect all active platform ids --}}
        var platform_ids = [];
        $('.platform-filter-active').each(function() {
            platform_ids.push($(this).data("id"))
        });
        {{-- Collect all active options --}}
        var options = [];
        $('.option-filter-active').each(function() {
            options.push($(this).data("filter"))
        });
        $.ajax({
            url:'{{ url("listings/filter") }}',
            type: 'POST',
            data: {platformIds:platform_ids, options: options},
            {{-- Send CSRF Token over ajax --}}
            headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
            success: function (data) {
              ajaxLoad(data, function(){
                $('#filter-submit').html('<i class="icon fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }}');
                $('#filter-submit').removeClass('loading');
                $('#modal_filter').modal('hide');
              });
            }
        });
    });

    {{-- Order by change URL --}}
    $('#order_by').change(function () {
        var goToUrl = $(this).val();
        ajaxLoad(goToUrl);
    });

    {{-- Remove all active filter --}}
    $('#remove-filter').click(function (e) {
      e.preventDefault();
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      ajaxLoad($(this).attr('href'), function() {
        $(".platform-filter-active").css("background-color", "");
        $(".platform-filter").removeClass("platform-filter-active");
      });
    });

    {{-- Change order direction --}}
    $('#order-direction').click(function (e) {
      e.preventDefault();
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      ajaxLoad($(this).attr('href'));
    });


  });
  </script>
  @endsection

@stop
