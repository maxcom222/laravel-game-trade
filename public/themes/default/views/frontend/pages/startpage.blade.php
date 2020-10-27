@extends(Theme::getLayout())

@section('subheader')

  {{-- Load landing page --}}
  @if(config('settings.landing_page') && !Auth::check())
    @include('frontend.pages.inc.landing_page')
  @endif


  {{-- Load carousel --}}
  @if(config('settings.frontpage_carousel'))
    @include('frontend.pages.inc.slider')
  @elseif(!config('settings.landing_page') || Auth::check())
    <div style="position: relative">
      <div class="page-top-background">
        <div class="background-overlay" id="parallax"></div>
      </div>
    </div>
  @endif

@stop

@section('content-full-width')

{{-- Load Google AdSense --}}
@if(config('settings.google_adsense'))
  @include('frontend.ads.google')
@endif
<div class="page">
  <div class="page-content container-fluid" >
    {{-- Title "Newest Listings" --}}
    <div class="m-b-30 flex-center-space">
      {{-- Title with active listings count --}}
      <div>
        <a href="{{ route('listings') }}" class="title-button without-padding"><span><i class="fa fa-tags m-r-10" aria-hidden="true"></i>{{ trans('listings.general.newest_listings') }}</span>@if(isset($listings) && count($listings->where('created_at','>', Carbon\Carbon::now()->subDays(1))) >= 1)<span class="new-items"><i class="fas fa-plus-circle"></i>{{ count($listings->where('created_at','>', Carbon\Carbon::now()->subDays(1)))}}</span>@endif</a>
      </div>
      {{-- Show all link --}}
      <div>
        @if(isset($listings) && count($listings) == 24)
        <a href="{{ url('listings') }}" class="title-button round"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
        @endif
      </div>
    </div>

    {{-- START LISTINGS --}}
    <div class="row">

      @forelse($listings as $listing)
        @include('frontend.listing.inc.card')
      @empty
        {{-- Start empty list message --}}
        <div class="empty-list add-button">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
            {{ trans('listings.general.no_listings') }}
          </div>
          {{-- Create listing button --}}
          @if(Auth::check())
          <a href="{{ url('listings/add' ) }}" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
          @else
          <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
          @endif
        </div>
        {{-- End empty list message --}}
      @endforelse


      {{-- Show more link on bottom --}}
      @if(count($listings) == 24)
        <div class="text-center m-b-30 m-t-20">
          <a href="{{ url('listings') }}" class="title-button round"><i class="fas fa-ellipsis-h"></i></a>
        </div>
      @endif

    </div>
    {{-- END LISTINGS --}}
  </div>
</div>

{{-- START POPULAR GAMES --}}
@if(isset($popular_games) && $popular_games->count() > 0)
<div class="sub-background">
  <div style="position: relative">
    <div class="page-top-background">
      <div class="background-overlay" id="parallax"></div>
    </div>
  </div>
  <div class="page sub-page">
    <div class="page-content container-fluid" >
      {{-- Title "Popular Games" --}}
      <div class="flex-center-space m-t-30 m-b-30">
        {{-- Title with active listings count --}}
        <div>
          <a href="{{ route('games') }}" class="title-button"><i class="fa fa-gamepad m-r-10" aria-hidden="true"></i>{{ trans('games.general.popular_games') }}</a>
        </div>
        {{-- Show all link --}}
        <div>
          <a href="{{ route('games') }}" class="title-button round"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
        </div>
      </div>

      {{-- START POPULAR GAMES --}}
      <div class="row">

        @foreach($popular_games as $game)
          @include('frontend.game.inc.card')
        @endforeach


        {{-- Show more link on bottom --}}
        <div class="text-center m-b-10 m-t-10">
          <a href="{{ url('games') }}" class="title-button round"><i class="fas fa-ellipsis-h"></i></a>
        </div>

      </div>
      {{-- END POPULAR GAMES --}}
    </div>
  </div>
</div>
@endif
{{-- END POPULAR GAMES --}}

{{-- START PLATFORMS --}}
<div class="platform-footer">
  <div class="row no-space">
    @foreach($platforms as $platform)
      <a class="col-xs-6 col-sm-4 col-md-4 col-lg-2" href="{{ $platform->url }}">
        <div class="platform">
          @if( config('settings.platform_logo') )
            <img src="{{ asset('logos/' . $platform->acronym . '_tiny.png/') }}" alt="{{$platform->name}} Logo">
          @else
            <span>{{ $platform->name }}</span>
          @endif
        </div>
      </a>
    @endforeach
  </div>
</div>
{{-- END PLATFORMS --}}


@stop

{{-- Start Breadcrumbs
@section('breadcrumbs')
{!! Breadcrumbs::render('home') !!}
@endsection
End Breadcrumbs --}}

@section('after-scripts')
    <script>
      $(document).ready(function(){

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
                    window.location=data;
                }
            });
        });

        {{-- Order by change URL --}}
        $('#order_by').change(function () {
            var goToUrl = $(this).val();
            window.location.replace(goToUrl);
            window.location.href = goToUrl;
        });

        {{-- Load carousel JS Settings --}}
        @if(config('settings.frontpage_carousel'))
        var releaseCarousel = $(".owl-carousel");
        releaseCarousel.on('initialize.owl.carousel',function(){
            releaseCarousel.addClass('carousel-loaded');
        });

        releaseCarousel.owlCarousel({
                autoplay: true,
                nav:false,
                dots:false,
                lazyLoad: true,
                loop: true,
                items : 4, //4 items above 1000px browser width
                responsive:{
                    0:{
                        items:1
                    },
                    500:{
                        items:2
                    },
                    900:{
                        items:3
                    },
                    1100:{
                        items:4
                    },
                    1500:{
                        items:5
                    }
                }
        });
        @endif
      });
    </script>
@stop
