@section('subheader')
<div class="subheader-image-bg">
  <div class="bg-image-wrapper">
    {{-- Background image of subheader --}}
    @if($game->image_cover)
      <div class="bg-image lazy" data-original="{{$game->image_cover}}"></div>
      <div style="position: absolute; height: 500px; width: 100%; top: 0; background: linear-gradient(0deg, rgba(25,24,24,1) 30%, rgba(25,24,24,0) 80%);"></div>
    {{-- Default background when image cover is missing --}}
    @else
    <div class="bg-image no-image" style="background: linear-gradient(0deg, rgba(25,24,24,1) 0%, rgba(25,24,24,1) 30%, rgba(25,24,24,0) 80%), url({{ asset('/img/game_pattern_white.png') }});"></div>
    @endif
  </div>
  {{-- background color overlay --}}
  <div class="bg-color"></div>
</div>

{{-- Listing sold overlay --}}
@if((isset($listing) && ($listing->status != 0 && !is_null($listing->status))) || isset($listing) && !$listing->user->isActive() )
  <div class="listing-sold-overlay flex-center">
    <div class="msg">
      <div class="msg bg-danger">
        <i class="fa fa-times"></i> {{ trans('listings.general.sold') }}
      </div>
      {{-- Gameover Button --}}
      <div class="m-t-20 text-center">
        <a class="gameoverview-button" href="{{ $game->url_slug }}"><i class="fa fa-angle-double-right" aria-hidden="true"></i> {{ trans('listings.overview.subheader.go_gameoverview') }}</a>
      </div>
    </div>
  </div>
@endif


@endsection

@section('game-content')

{{-- SEO Start --}}
<div itemscope itemtype="http://schema.org/Product" class="hidden">
  {{-- Game name --}}
  <meta itemprop="name" content="{{ $game->name }}" />
  {{-- Game cover --}}
@if($game->cover)
  <meta itemprop="image" content="{{ $game->image_cover }}" />
@endif
  {{-- Game release date --}}
@if($game->release_date)
  <meta itemprop="releaseDate" content="{{ $game->release_date->format('Y-m-d') }}" />
@endif
  {{-- Game description --}}
@if($game->description)
  <meta itemprop="description" content="{{ $game->description }}" />
@endif
@if(isset($listing) && $listing->sell)
  {{-- User rating --}}
  @if($listing->user->ratings->count() > 0)
  <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="hidden">
    <meta itemprop="ratingValue" content="{{ ($listing->user->positive_percent_ratings * 5)/100 }}" />
    <meta itemprop="reviewCount" content="{{ $listing->user->ratings->count() }}" />
  </div>
  @endif
  {{-- Listing details --}}
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="hidden">
    <meta itemprop="url" content="{{ $listing->url_slug }}" />
    <meta itemprop="price" content="{{ $listing->price_decimal }}" />
    <meta itemprop="priceCurrency" content="{{ Config::get('settings.currency') }}" />
    <meta itemprop="availability" content="http://schema.org/InStock" />
    <meta itemprop="itemCondition" content="{{ $listing->condition == 5 ? 'http://schema.org/NewCondition' : 'http://schema.org/UsedCondition' }}" />
    <div itemprop="seller" itemscope itemtype="http://schema.org/Person" class="hidden">
      <meta itemprop="name" content="{{ $listing->user->name }}" />
      <meta itemprop="url" content="{{ $listing->user->url }}" />
    </div>
  </div>
@elseif($game->listings)
  {{-- All offers for a game --}}
  <div itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" class="hidden">
    <meta itemprop="offerCount" content="{{ $game->listings->count() }}" />
    <meta itemprop="priceCurrency" content="{{ Config::get('settings.currency') }}" />
    <meta itemprop="lowPrice" content="{{ $game->lowestPrice }}" />
    <meta itemprop="highPrice" content="{{ $game->highestPrice }}" />
  </div>
@endif
</div>
{{-- SEO End --}}

<div class="row no-space equal">
  <div class="offset-xs-3 col-xs-6 offset-sm-0 col-sm-4 col-md-3 offset-md-0 col-lg-3 col-xxl-2 {{ isset($listing) ? 'game-cover-sticky' : '' }}">

    {{-- Start Game Cover --}}
    <div class="game-cover-wrapper shadow">
      {{-- Pacman Loader for background image - show only when cover exists --}}
      @if($game->image_cover)
      <div class="loader pacman-loader cover-loader"></div>
      {{-- Show game name, when no cover exist --}}
      @else
      <div class="no-cover-name">{{$game->name}}</div>
      @endif

      {{-- Digital download icon --}}
      @if(isset($listing) && $listing->digital)
      <div class="animation-scale-up digital-download">
        <i class="fa fa-download" aria-hidden="true"></i>
      </div>
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
    </div>
    {{-- End Game Cover --}}

    {{-- Game scores (Metacritic / Userscore) --}}
    @if(isset($game->metacritic) && ($game->metacritic->score || $game->metacritic->userscore))
    <div>
      <div class="text-center game-cover-scores" style="">
        {{-- Metascore --}}
        @if(isset($game->metacritic) && $game->metacritic->score)
        <a href="{{$game->metacritic->url}}" target="_blank" class="metascore round @if(isset($game->metacritic->userscore)) m-r-5 @endif {{$game->metacritic->score_class}}">
           <span class="score">{{$game->metacritic->score}}</span>
        </a>
        @endif
        {{-- Userscore --}}
        @if(isset($game->metacritic) && $game->metacritic->userscore)
        <a href="{{$game->metacritic->url}}" target="_blank" class="metascore round user">
           <span class="score">{{$game->metacritic->userscore}}</span>
        </a>
        @endif
      </div>
    </div>
    @endif
    {{-- Show buttons only on big screens --}}
    <div class="hidden-sm-down">
      {{-- Buttons for listing --}}
      @if(isset($listing))
        {{-- Start Buy Button --}}
        @if($listing->sell)
          <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-buy' : '#LoginModal' }}" class="buy-button m-b-10 @if(!isset($game->metacritic) || (isset($game->metacritic) && !($game->metacritic->score || $game->metacritic->userscore))) m-t-10 @endif flex-center-space">
            <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
            <span class="text">{{ $listing->getPrice() }}</span>
            {{-- Check if user allow price suggestions --}}
            @if($listing->sell_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @else
              <span></span>
            @endif
          </a>
        @endif
        {{-- End Buy Button --}}

        {{-- Start Trade Button --}}
        @if($listing->trade)
          <a href="javascript:void(0);" class="trade-button m-b-10 {{ $listing->sell ? '' : 'm-t-20'}} flex-center-space" @if($listing->trade_negotiate && !isset($trade_list)) data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_suggestion' : '#LoginModal' }}" @else id="trade-button-subheader" @endif>
            <i class="icon fa fa-exchange" aria-hidden="true"></i><span class="text">{{ trans('listings.general.trade') }}</span>
            {{-- Check if user allow trade suggestions --}}
            @if($listing->trade_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @else
              <span></span>
            @endif
          </a>
        @endif
        {{-- End Trade Button --}}
        {{-- Send Message Button --}}
        {{-- Check if logged in user is listing user --}}
        @if(!(Auth::check() && Auth::user()->id == $listing->user_id))
          <div class="m-t-10">
            <a class="message-button btn-dark flex-center-space" href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}"><i class="icon fas fa-envelope-open m-r-5"></i>{{ trans('messenger.send_message') }}<span></span></a>
          </div>
        @endif
        {{-- End Message Button --}}

      @else
        {{-- Load Buy Button Ref Link --}}
        @if(config('settings.buy_button_ref'))
          @include('frontend.ads.buyref')
        @endif
        {{-- Available on different platforms --}}
        @if(isset($different_platforms) && count($different_platforms)>0)
          {{-- Platform list --}}
          <div class="glist">
            @foreach($different_platforms as $different_platform)
            <a href="{{ $different_platform->url_slug }}" >
              <div onMouseOver="this.style.backgroundColor='{{ $different_platform->platform->color }}'" onMouseOut="this.style.backgroundColor=''" class="gitem @if($loop->first && !config('settings.buy_button_ref') && (isset($game->metacritic) && !$game->metacritic->score && !$game->metacritic->userscore)) m-t-20 @endif" style="border: 2px solid {{$different_platform->platform->color}};">
                {{-- Check if platform logo setting is enabled --}}
                @if( config('settings.platform_logo') )
                  <img src="{{ asset('logos/' . $different_platform->platform->acronym . '_tiny.png/') }}" alt="{{$different_platform->platform->name}} Logo">
                @else
                  <span>{{$different_platform->platform->name}}</span>
                @endif
              </div>
            </a>
            @endforeach
          </div>
        @endif
        <div class="gsummary">
          {!! $game->description !!}
        </div>
      @endif
    </div>
  </div>


  <div class="col-xs-12 col-sm-8 col-md-9 col-lg-9 col-xxl-10">

    {{-- Start Game Details --}}
    <div class="game-details flex-center-space">
      <div class="ginfo">

        {{-- Game title with release year --}}
        <div class="flex-center-space">
          <div class="gtitle">
            {{$game->name}} @if(isset($game->release_date))<span class="release-year">{{$game->release_date->format('Y')}}</span>@endif
          </div>
          @if($game->heartbeat->count() > 0)
          {{-- Game heartbeat --}}
          <div class="heartbeat">
            <i class="fas fa-heartbeat"></i> {{ $game->heartbeat->count() }}
          </div>
          @endif
        </div>
        {{-- Buttons related to the game --}}
        <div class="gbuttons">
          {{-- Wishlist button --}}
          @if(!isset($game->wishlist))
            <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#AddWishlist' : '#LoginModal' }}" class="btn btn-round"><i class="fas fa-heart"></i> {{ trans('wishlist.add_wishlist') }}</a>
          {{-- On your wishlist with delete button --}}
          @else
            <a href="javascript:void(0);" data-toggle="modal" data-target="#EditWishlist_{{$game->wishlist->id}}" class="on-wishlist"><i class="fas fa-heart"></i> {{ trans('wishlist.on_wishlist') }}</a><a href="{{ $game->url_slug }}/wishlist/delete" class="btn btn-round delete-wishlist">{{ trans('general.delete') }}</a>
          @endif
          {{-- Go to gameoverview button --}}
          @if(isset($listing))
          <a href="{{ $game->url_slug }}" class="btn btn-round m-l-5"><i class="fas fa-gamepad"></i><span class="hidden-xs-down"> {{ trans('listings.overview.subheader.go_gameoverview') }}</a></span>
          @endif
        </div>

        <div class="hidden-md-up">
          {{-- Buttons for listing --}}
          @if(isset($listing))
            <div class="flex-center-space">
              {{-- Start Buy Button --}}
              @if($listing->sell)
              <div class="button-fix m-t-20 {{ $listing->trade ? 'm-r-5' : ''}}">
                  <a href="javascript:void(0);" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-buy' : '#LoginModal' }}" class="buy-button flex-center-space">
                    <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
                    <span class="text">{{ $listing->getPrice() }}</span>
                    {{-- Check if user allow price suggestions --}}
                    @if($listing->sell_negotiate)
                      <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
                    @else
                      <span></span>
                    @endif
                  </a>
              </div>
              @endif
              {{-- End Buy Button --}}
              {{-- Start Trade Button --}}
              @if($listing->trade)
              <div class="button-fix m-t-20 {{ $listing->sell ? 'm-l-5' : ''}}">
                  <a href="javascript:void(0);" class="trade-button flex-center-space" @if($listing->trade_negotiate && !isset($trade_list)) data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_suggestion' : '#LoginModal' }}" @else id="trade-button-subheader-mobile" @endif>
                    <i class="icon fa fa-exchange" aria-hidden="true"></i><span class="text">{{ trans('listings.general.trade') }}</span>
                    {{-- Check if user allow trade suggestions --}}
                    @if($listing->trade_negotiate)
                      <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
                    @else
                      <span></span>
                    @endif
                  </a>
              </div>
              @endif
              {{-- End Trade Button --}}
            </div>

            {{-- Send Message Button --}}
            {{-- Check if logged in user is listing user --}}
            @if(!(Auth::check() && Auth::user()->id == $listing->user_id))
              <div class="m-t-10">
                <a class="message-button btn-dark flex-center-space" href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}"><i class="icon fas fa-envelope-open m-r-5"></i>{{ trans('messenger.send_message') }}<span></span></a>
              </div>
            @endif
            {{-- End Message Button --}}
          @else
            <div class="gsummary m-b-10">
              {!! $game->description !!}
            </div>
            {{-- Load Buy Button Ref Link --}}
            @if(config('settings.buy_button_ref'))
              @include('frontend.ads.buyref')
            @endif
            {{-- Available on different platforms --}}
            @if(isset($different_platforms) && count($different_platforms)>0)
              {{-- Platform list --}}
              <div class="glist">
                @foreach($different_platforms as $different_platform)
                <a href="{{ $different_platform->url_slug }}" >
                  <div onMouseOver="this.style.backgroundColor='{{ $different_platform->platform->color }}'" onMouseOut="this.style.backgroundColor=''" class="gitem" style="border: 2px solid {{$different_platform->platform->color}};">
                    {{-- Check if platform logo setting is enabled --}}
                    @if( config('settings.platform_logo') )
                      <img src="{{ asset('logos/' . $different_platform->platform->acronym . '_tiny.png/') }}" alt="{{$different_platform->platform->name}} Logo">
                    @else
                      <span>{{$different_platform->platform->name}}</span>
                    @endif
                  </div>
                </a>
                @endforeach
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>
    {{-- End Game Details --}}
  </div>

@stop
