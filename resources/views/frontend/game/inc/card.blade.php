{{-- START GAME --}}
<div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">
  {{-- Start Game Cover --}}
  <div class="card game-cover-wrapper hvr-grow-shadow" style="margin-bottom: 5px;">
    {{-- Show "New!" label if item or price is not older than 1 day --}}
    @if(Carbon\Carbon::now()->subDays(1) < $game->created_at )
      <div class="item-new {{ $game->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
    @endif
    {{-- Pacman Loader for background image - show only when cover exists --}}
    @if($game->image_cover)
    {{-- <div class="loader pacman-loader cover-loader"></div> --}}
    {{-- Show game name, when no cover exist --}}
    @else
    <div class="no-cover-name">{{$game->name}}</div>
    @endif

    <a href="{{ $game->url_slug }}">

      {{-- Check if game is on the wishlist --}}
      @if(Auth::check())
        {{-- Check if game id is in wishlist of user --}}
        @if(Auth::user()->wishlists()->contains('game_id', $game->id))
          {{-- (Heart icon) On your Wishlist --}}
          <div class="on-wishlist {{ $game->cover_generator ? 'with-platform' : ''  }} {{ Carbon\Carbon::now()->subDays(1) < $game->created_at ? 'with-new' : ''  }}">
            <i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}
          </div>
        @endif
      @endif

      {{-- Generated game cover with platform on top --}}
      @if($game->cover_generator)
        <div class="lazy game-cover gen"  data-original="{{$game->image_cover}}"></div>
        <div class="game-platform-gen" style="background-color: {{$game->platform->color}}; text-align: {{$game->platform->cover_position}};">
          {{-- Check if platform logo setting is enabled --}}
          @if( config('settings.platform_logo') )
            <img src="{{ asset('logos/' . $game->platform->acronym . '_tiny.png/') }}" alt="{{$game->platform->name}} Logo">
          @else
            <span>{{$game->platform->name}}</span>
          @endif
        </div>
      {{-- Normal game cover --}}
      @else
        <div class="lazy game-cover"  data-original="{{$game->image_cover}}"></div>
      @endif
      {{-- Item name --}}
      @if($game->image_cover)
      <div class="item-name">
        {{ $game->name }}
      </div>
      @endif
    </a>
  </div>
  {{-- End Game Cover --}}

  @if($game->listingsCount > 0 || $game->wishlistCount > 0 || isset($game->metacritic))
  <div class="listing-details flex-center-space">
    @if($game->listingsCount > 0 || $game->wishlistCount > 0)
    <div class="listing-active-wrapper">
      @if($game->listingsCount > 0)
      <div class="listing-active @if($game->wishlistCount > 0) with-game-popularity @endif">
          <i class="fa {{ $game->listingsCount == 1 ? 'fa-tag' : 'fa-tags' }}"></i> {{ $game->listingsCount }}
      </div>
      @endif
      @if($game->wishlistCount > 0)
      <div class="game-popularity @if($game->listingsCount > 0) with-listing-active @endif">
        <i class="fa fa-heartbeat"></i> {{ $game->wishlistCount }}
      </div>
      @endif
    </div>
    @else
    <div></div>
    @endif
    @if(isset($game->metacritic) && $game->metacritic->score)
    <div class="listing-active {{$game->metacritic->score_class}}">
      {{ $game->metacritic->score }}
    </div>
    @endif
  </div>
  @endif

</div>
{{-- End GAME --}}
