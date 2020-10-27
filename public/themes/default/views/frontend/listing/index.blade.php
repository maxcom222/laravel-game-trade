@extends(Theme::getLayout())

@section('subheader')

<div style="position: relative;  height: 0px; ">


  <div class="page-top-background" style="position: absolute; z-index:0 !important; top: 0; width: 100%;">
    @if(!is_null($system))
    <div style="background-color: {{$system->color}}; height: 400px; margin-top: -60px; z-index: 0; position: relative;"></div>
    @endif

    <div class="background-overlay listings-overview {{!is_null($system) ? 'with-platform' : ''}}"></div>

  </div>

</div>

@stop



@section('content')

@if(!is_null($system))

<div style="margin-bottom: 50px;">
  {{-- Check if platform logo setting is enabled --}}
  @if( config('settings.platform_logo') )
    <img src="{{ asset('logos/' . $system->acronym . '.png/') }}" alt="" height="40">
  @else
    <span class="platform-title">{{$system->name}}</span>
  @endif
</div>

@endif

{{-- Load Google AdSense --}}
@if(config('settings.google_adsense'))
  @include('default::frontend.ads.google')
@endif

{{-- Listings title --}}
<div class="panels-title border-bottom flex-center-space">
  {{-- Title --}}
  <div>
    <i class="fa fa-tags" aria-hidden="true"></i> {{ trans('general.listings') }}
  </div>
  {{-- Current page + page count --}}
  <div class="o-50">
    {{-- First check if pages exist --}}
    @if($listings->lastPage())
    <span id="current-page">{{ $listings->currentPage() }}</span> / <span id="last-page">{{ $listings->lastPage() }}</span>
    @endif
  </div>
</div>

{{-- Start Listings wrapper --}}
<div id="listings-wrapper">
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
          <a href="{{ url('listings/filter/remove') }}" class="m-l-5 btn btn-dark" id="remove-filter">
              <i class="fa fa-times" aria-hidden="true"></i>
          </a>
          @endif
      </div>
      {{-- End Filter button --}}
      {{-- Start sort options --}}
      <div>
          {{-- Sort order button (desc / asc) --}}
          <a href="{{ url('listings/order') }}/{{ session()->has('listingsOrder') ? session()->get('listingsOrder') : 'created_at' }}/{{  session()->has('listingsOrderByDesc') ? (session()->get('listingsOrderByDesc') ? 'asc' : 'desc') : 'asc' }}" class="btn btn-dark" style="vertical-align: inherit;" id="order-direction">
              <i class="fa fa-sort-amount-{{ session()->has('listingsOrderByDesc') ? (session()->get('listingsOrderByDesc') ? 'up' : 'down') : 'up' }}" aria-hidden="true"></i>
          </a>
          {{-- Sort dropdown --}}
          <div class="m-l-5 inline-block">
              <select id="order_by" class="form-control select" style="height: 33px !important;">
                  {{-- Sort by --}}
                  <option disabled>{{ trans('general.sortfilter.sort_by') }}</option>
                  {{-- Created at option --}}
                  <option value="{{ url('listings/order/created_at') }}" {{ session()->has('listingsOrder') ? (session()->get('listingsOrder') == 'created_at' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_date') }}</option>
                  {{-- Price option --}}
                  <option value="{{ url('listings/order/price') }}" {{ session()->has('listingsOrder') ? (session()->get('listingsOrder') == 'price' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_price') }}</option>
                  {{-- Distance option --}}
                  @if((\Auth::check() && (\Auth::user()->location && \Auth::user()->location->longitude && \Auth::user()->location->latitude)) || (session()->has('latitude') && session()->has('longitude')))
                  <option value="{{ url('listings/order/distance') }}" {{ session()->has('listingsOrder') ? (session()->get('listingsOrder') == 'distance' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_distance') }}</option>
                  @endif
              </select>
          </div>
      </div>
      {{-- End sort options --}}
  </div>
  {{-- End Filter / Sort options --}}

  {{-- START LISTINGS --}}
  <div class="row">

    @forelse($listings as $listing)
      @include('frontend.listing.inc.card')
    @empty
      {{-- Start empty list message --}}
      <div class="no-listings">
        <div class="empty-list">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
            {{ trans('listings.general.no_listings') }}
          </div>
        </div>
      </div>
      {{-- End empty list message --}}
    @endforelse

  </div>
  {{-- END LISTINGS --}}


  {{ $listings->links() }}

</div>
{{-- End Listings Wrapper --}}

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
      @if(is_null($system))
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
      @endif
      {{-- Start option filters --}}
      <div class="modal-seperator">
          {{ trans('general.sortfilter.filter_options') }}
      </div>
      <div class="modal-body">
          @php
              $active_options = session()->get('listingsOptionFilter') ?  session()->get('listingsOptionFilter') : [];
          @endphp
          {{-- Sell option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('sell', $active_options) ? 'option-filter-active' : '' }}" data-filter="sell">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i> {{ trans('listings.general.sell') }}
          </a>
          {{-- Trade option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('trade', $active_options) ? 'option-filter-active' : '' }}" data-filter="trade">
              <i class="fa fa-exchange" aria-hidden="true"></i> {{ trans('listings.general.trade') }}
          </a>
          {{-- Pickup option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('pickup', $active_options) ? 'option-filter-active' : '' }}" data-filter="pickup">
              <i class="far fa-handshake" aria-hidden="true"></i> {{ trans('listings.general.pickup') }}
          </a>
          {{-- Delivery option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('delivery', $active_options) ? 'option-filter-active' : '' }}" data-filter="delivery">
              <i class="fa fa-truck" aria-hidden="true"></i> {{ trans('listings.general.delivery') }}
          </a>
          {{-- Digital download option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('digital', $active_options) ? 'option-filter-active' : '' }}" data-filter="digital">
              <i class="fa fa-download" aria-hidden="true"></i> {{ trans('listings.form.details.digital') }}
          </a>
          {{-- Secure payment option --}}
          <a href="#" class="label platform-label option-filter m-r-5 m-b-5 inline-block {{ in_array('payment', $active_options) ? 'option-filter-active' : '' }}" data-filter="payment">
              <i class="fa fa-shield-check" aria-hidden="true"></i> {{ trans('payment.secure_payment') }}
          </a>
      </div>
      {{-- End option filters --}}
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
  {{-- Breadcrumbs for all listings --}}
  @if(is_null($system))
    {!! Breadcrumbs::render('listings') !!}
  {{-- Breadcrumbs for platform listings --}}
  @else
    {!! Breadcrumbs::render('platform_listings', $system) !!}
  @endif
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
        $('#listings-wrapper').html(data);
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

    {{-- Option filter --}}
    $('.option-filter').click(function(e) {
        e.preventDefault();
        $(this).toggleClass('option-filter-active')
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
        $(".option-filter-active").css("background-color", "");
        $(".option-filter").removeClass("option-filter-active");
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
