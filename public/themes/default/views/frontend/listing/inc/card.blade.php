{{-- START GAME --}}
<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">

  {{-- Start Game Cover --}}
  <div class="card game-cover-wrapper hvr-grow-shadow"  style="margin-bottom: 0px;">
    {{-- Show "New!" label if item or price is not older than 1 day --}}
    @if(Carbon\Carbon::now()->subDays(1) < $listing->created_at )
      <div class="item-new {{ $listing->game->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
    @endif
    {{-- Pacman Loader for background image - show only when cover exists --}}
    @if($listing->game->image_cover)
      {{--
    <div class="loader pacman-loader cover-loader"></div> --}}
    {{-- Show game name, when no cover exist --}}
    @else
    <div class="no-cover-name">{{$listing->game->name}}</div>
    @endif

    <a href="{{ $listing->url_slug }}">

      {{-- Check if game is on the wishlist --}}
      @if(Auth::check())
        {{-- Check if game id is in wishlist of user --}}
        @if(Auth::user()->wishlists()->contains('game_id', $listing->game->id))
          {{-- (Heart icon) On your Wishlist --}}
          <div class="on-wishlist {{ $listing->game->cover_generator ? 'with-platform' : ''  }} {{ Carbon\Carbon::now()->subDays(1) < $listing->created_at ? 'with-new' : ''  }}">
            <i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}
          </div>
        @endif
      @endif

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
        <div class="lazy game-cover gen"  data-original="{{$listing->game->image_cover}}"></div>
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
        <div class="lazy game-cover"  data-original="{{$listing->game->image_cover}}"></div>
      @endif
      {{-- Item name --}}
      @if($listing->game->image_cover)
      <div class="item-name">
        {{ $listing->game->name }} @if($listing->limited_edition)<div><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}</div>@endif
      </div>
      @elseif($listing->limited_edition)
      <div class="item-name">
        <i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>
      </div>
      @endif
      @if($listing->picture)
      <div class="lazy item-image" data-original="{{ $listing->picture_square }}"></div>
      @endif
    </a>
  </div>
  {{-- End Game Cover --}}


  <div class="listing-details flex-center-space" style="margin-top: 5px;">
    @if($listing->sell)
    <div class="listing-price">
      {{ $listing->getPrice() }}
    </div>
    @else
    <div>
    </div>
    @endif
    @if($listing->trade)
    <div class="listing-trade @if($listing->sell) with-price @endif" class="no-flex-shrink">
        <i class="fa fa-exchange"></i>
    </div>
    @endif
  </div>

  {{-- Start User info --}}
  <div class="game-user-details" style="margin-top: 0px !important; ">
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
