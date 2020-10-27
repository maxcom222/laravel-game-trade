<div class="row">
@forelse($tradegames as $listing)
  {{-- START GAME --}}
  <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">
    {{-- Start Game Cover --}}
    <div class="card game-cover-wrapper hvr-grow-shadow">
      {{-- Pacman Loader for background image - show only when cover exists --}}
      @if($listing->game->image_cover)
      <div class="loader pacman-loader cover-loader"></div>
      {{-- Show game name, when no cover exist --}}
      @else
      <div class="no-cover-name">{{$listing->game->name}}</div>
      @endif

      <a href="{{ $listing->url_slug }}">
        {{-- Start Additional Charge Ribbon --}}
        @if($listing->pivot->price_type != 'none')
          @if($listing->pivot->price_type == 'want')
          <div class="ribbon ribbon-clip ribbon-bottom ribbon-danger">
          @elseif($listing->pivot->price_type == 'give')
          <div class="ribbon ribbon-clip ribbon-bottom ribbon-success">
          @endif
            <div class="ribbon-inner">
              @if($listing->pivot->price_type == 'want')
              <span class="currency"><i class="fa fa-minus"></i></span>
              @elseif($listing->pivot->price_type == 'give')
              <span class="currency"><i class="fa fa-plus"></i></span>
            @endif<span class="price"> {{ money($listing->pivot->price, Config::get('settings.currency')) }}</span>
            </div>
          </div>
        @endif
        {{-- End Additional Charge Ribbon --}}

        {{-- Payment icon --}}
        @if($listing->payment)
        <div class="animation-scale-up payment-enabled">
          <i class="fa fa-shield-check" aria-hidden="true"></i>
        </div>
        @endif

        {{-- Digital download icon --}}
        @if($listing->digital)
        <div class="animation-scale-up digital-download {{ $listing->payment ? 'with-payment' : '' }}">
          <i class="fa fa-download" aria-hidden="true"></i>
        </div>
        @endif

        {{-- Pickup icon --}}
        @if($listing->pickup)
        <div class="pickup-icon {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
          <i class="far fa-handshake" aria-hidden="true"></i>
        </div>
        @endif

        {{-- Delivery icon --}}
        @if($listing->delivery)
        <div class="delivery-icon {{ $listing->pickup ? 'with-pickup' : '' }} {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
          <i class="fa fa-truck" aria-hidden="true"></i>
        </div>
        @endif

        {{-- Generated game cover with platform on top --}}
        @if($listing->game->cover_generator)
          <div class="lazy-trade game-cover gen"  data-original="{{$listing->game->image_cover}}"></div>
          <div class="game-platform-gen" style="background-color: {{$listing->game->platform->color}}; text-align: {{$listing->game->platform->cover_position}};">
            {{-- Check if platform logo setting is enabled --}}
            @if( config('settings.platform_logo') )
              <img src="{{ asset('logos/' . $listing->game->platform->acronym . '_tiny.png/') }}" alt="{{$listing->game->platform->name}} Logo">
            @else
              <span>{{$listing->game->platform->name}}</span>
            @endif
          </div>
        {{-- Normal game cover --}}
        @else
          <div class="lazy-trade game-cover"  data-original="{{$listing->game->image_cover}}"></div>
        @endif
        {{-- Item name --}}
        @if($listing->game->image_cover)
        <div class="item-name">
          {{ $listing->game->name }}
        </div>
        @endif
      </a>
    </div>
    {{-- End Game Cover --}}

    {{-- Start User info --}}
    <div class="game-user-details">
      {{-- Distance --}}
      @if($listing->distance !== false)
      <span class="distance">
        <i class="fa fa-location-arrow" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}
      </span>
      @endif
      {{-- User avtar and name --}}
      <a href="{{ $listing->user->url }}" class="user-link">
        <span class="avatar avatar-xs @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif">
          <img src="{{$listing->user->avatar_square_tiny}}" alt="{{$listing->user->name}}'s Avatar"><i></i>
        </span>
        {{$listing->user->name}}
      </a>
    </div>
    {{-- End User info --}}
  </div>
  {{-- End GAME --}}
@empty
  {{-- Start empty list message --}}
  <div class="no-listings">
    <div class="empty-list add-button">
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
<script>
$("div.lazy-trade").lazyload({
    effect : "fadeIn",
    load : function(elements_left, settings) {
      $(this).parent().parent().find('.pacman-loader').delay(200).fadeOut();
    }
});
</script>
