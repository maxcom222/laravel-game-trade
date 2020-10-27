@extends(Theme::getLayout())

{{-- Load game subheader --}}
@include('frontend.game.subheader')

@section('content')

{{-- Load content from game subheader --}}
@yield('game-content')

{{-- Start Subheader tabs --}}
<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xxl-10">
  {{-- Start Item Content --}}
  <div class="item-content">
    <div class="subheader-tabs-wrapper flex-center-space">
      {{-- Start Nav tabs --}}
      <div class="no-flex-shrink">
        <ul class="subheader-tabs" role="tablist">
          {{-- Details link --}}
          <li class="nav-item">
            <a data-toggle="tab" href="#details" data-target="#details" role="tab" class="subheader-link">
              <i class="fa fa-tag" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('listings.overview.subheader.details') }}</span>
            </a>
          </li>
          {{-- Media tab (Images & Videos) --}}
          @if($game->giantbomb_id)
          <li class="nav-item">
            <a data-toggle="tab" href="{{ url('/games/' . $game->id . '/media') }}" data-target="#media" role="tab" class="subheader-link">
              <i class="fa fa-images" aria-hidden="true"></i><span class="{{ config('settings.comment_listing') ? 'hidden-md-down' : 'hidden-xs-down'}}"> {{ trans('games.overview.subheader.media') }}</span>
            </a>
          </li>
          @endif
          {{-- Comments tab --}}
          @if(config('settings.comment_listing'))
          <li class="nav-item">
            <a data-toggle="tab" href="#comments" data-target="#comments" role="tab" class="subheader-link">
              <i class="fa fa-comments" aria-hidden="true"></i><span class="{{ config('settings.comment_listing') ? 'hidden-md-down' : 'hidden-xs-down'}}"> {{ trans('comments.comments') }}</span>
            </a>
          </li>
          @endif
        </ul>
      </div>
      {{-- End Nav tabs --}}
      {{-- Start Share buttons --}}
      <div @if(config('settings.comment_listing')) class="subheader-social-comments" @endif>
        {{-- Facebook share --}}
        <a href="https://www.facebook.com/dialog/share?
    {{config('settings.facebook_client_id') ? 'app_id='. config('settings.facebook_client_id') . '&' : '' }}display=popup&href={{URL::current()}}&redirect_uri={{ url('self.close.html')}}" onclick="window.open(this.href, 'facebookwindow','left=20,top=20,width=600,height=400,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-facebook m-r-5">
          <i class="icon fab fa-facebook-f" aria-hidden="true"></i>
        </a>
        {{-- Twitter share --}}
        @if($listing->sell == 1)
        <a href="http://twitter.com/intent/tweet?text={{trans('general.share.twitter_listing_buy', ['game_name' => $game->name, 'platform' => $game->platform->name, 'price' => $listing->price_formatted])}} &#8921; {{URL::current()}}" onclick="window.open(this.href, 'twitterwindow','left=20,top=20,width=600,height=300,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-twitter m-r-5">
          <i class="icon fab fa-twitter" aria-hidden="true"></i>
        </a>
        {{-- Twitter share with text for trade --}}
        @else
        <a href="http://twitter.com/intent/tweet?text={{trans('general.share.twitter_listing_trade', ['game_name' => $game->name, 'platform' => $game->platform->name])}} &#8921; {{URL::current()}}" onclick="window.open(this.href, 'twitterwindow','left=20,top=20,width=600,height=300,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-twitter m-r-5">
          <i class="icon fab fa-twitter" aria-hidden="true"></i>
        </a>
        @endif
      </div>
      {{-- End Share buttons --}}
    </div>
    {{-- End Subheader tabs --}}

    <div class="tab-content subheader-margin m-t-40 ">
      {{-- Load Google AdSense --}}
      @if(config('settings.google_adsense'))
        @include('frontend.ads.google')
      @endif

      @if(config('settings.comment_listing'))
      {{-- Start comments tab --}}
      <div class="tab-pane fade" id="comments" role="tabpanel">
        @php $item_type = 'listing'; $item_id = $listing->id; @endphp
        @include('frontend.comments.form')
      </div>
      {{-- End comments tab --}}
      @endif

    {{-- Start Listings tab --}}
    <div class="tab-pane fade" id="details" role="tabpanel">

      {{-- Start Listing values --}}
      <div class="listing-values">

        {{-- PayPal Payment --}}
        @if($listing->payment)
        <div class="value paypal-payment">
          <span class="p-10 inline-block">{{ trans('payment.secure_payment') }}</span><span class="p-10 inline-block protected"><i class="fa fa-shield-check" aria-hidden="true"></i></span>
        </div>
        @endif

        {{-- Limited edition --}}
        @if($listing->limited_edition)
        <div class="value">
          <i class="fa fa-star" aria-hidden="true"></i> {{$listing->limited_edition}}
        </div>
        @endif

        @if($listing->digital)
        {{-- Digital ditributor --}}
        <div class="value">
          <i class="fa fa-download" aria-hidden="true"></i> {{$listing->game->platform->digitals->where('id',$listing->digital)->first()->name}}
        </div>
        @else
        {{-- Condition --}}
        <div class="value">
          {{ trans('listings.general.condition') }}: {{$listing->condition_string}}
        </div>
        @endif

        {{-- Pickup --}}
        <div class="value">
          <i class="far fa-handshake" aria-hidden="true"></i>
          {{ trans('listings.general.pickup') }} <i class="fa @if($listing->pickup) fa-check-circle text-success @else fa-times-circle text-danger @endif" aria-hidden="true"></i>
        </div>

        {{-- Delivery --}}
        <div class="value">
          <i class="fa fa-truck" aria-hidden="true"></i>
          {{ trans('listings.general.delivery') }} <i class="fa @if($listing->delivery) fa-check-circle text-success @else fa-times-circle text-danger @endif" aria-hidden="true"></i>
          @if($listing->delivery && $listing->delivery_price != '0')
            <span class="delivery-price-span"> + {{ $listing->getDeliveryPrice() }}</span>
          @endif
        </div>

        {{-- Distance --}}
        @if($listing->distance !== false)
        <div class="value">
          <i class="fa fa-location-arrow" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}</span>
        </div>
        @endif
      </div>
      {{-- End Listing values --}}

      {{-- Start Listing Details --}}
      {{-- Description / Created at --}}
      <section class="panel">
        <div class="panel-body n-p">
          {{-- Description --}}
          @if($listing->description)
            <div class="listing-description p-20"> {!! $listing->description !!}</div>
          {{-- No Description --}}
          @else
            <div class="listing-description p-20 text-light flex-center"><i class="fa fa-ban m-r-5" aria-hidden="true"></i> {{ trans('listings.general.no_description') }}</div>
          @endif
        </div>

        {{-- User informations --}}
        <div class="panel-footer padding text-light">
          {{-- Listing Created at --}}
          <div>
            <i class="far fa-calendar-plus" aria-hidden="true"></i> {{ trans('listings.overview.created') }} {{$listing->created_at->diffForHumans()}}
          </div>
          {{-- Listing Clicks --}}
          <div>
            <i class="fa fa-chart-bar" aria-hidden="true"></i> {{$listing->clicks}} {{ trans('users.dash.listings.clicks') }}
          </div>
        </div>
      </section>

      {{-- Start Listing Images --}}
      @if(isset($listing->images))
        <div class="row listing-images">
          @foreach($listing->images as $image)
            {{-- Listimg image --}}
            <div class="col-md-3 col-xs-6 m-b-20">
              <a class="listing-image-wrapper" href="{{ $image->url }}" data-source="{{ $image->url }}" title="{{ $game->name }}" data-effect="mfp-zoom-in">
                {{-- Image (lazy loaded) --}}
                <div class="lazy overlay hvr-grow-shadow2 listing-image" data-original="{{ $image->thumbnail }}">
                  {{-- Pacman loader --}}
                  <div class="loader pacman-loader picture-loader"></div>
                  <div class="imgDescription"><div class="valign"><i class="fa fa-expand" aria-hidden="true"></i></div></div>
                </div>
                {{-- Image icon in top right corner --}}
                <i class="fa fa-image" aria-hidden="true"></i>
              </a>
            </div>
          @endforeach
        </div>
      @endif
      {{-- End Listing Images --}}

      {{-- Start Listing Trade --}}
      @if($listing->trade)
      {{-- Trade title --}}
      <div class="listing-title">
        <i class="fa fa-exchange" aria-hidden="true" id="trade-list"></i> {{ trans('listings.general.trade') }}
      </div>

      {{-- Trade info when user click button in subheader --}}
      <div class="panel panel-body hidden" id="trade-info">
        <i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('listings.overview.trade_info', ['game_name' => $game->name]) }}
      </div>

      {{-- Start Trade game list --}}
      <div class="row">
      {{-- Get additional charges --}}
      @php $add_charge = json_decode($listing->trade_list,true); @endphp
      @if($trade_list)
      @foreach($trade_list as $trade_game)
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2">
          {{-- Start Game Cover --}}
          <div class="card game-cover-wrapper hvr-grow-shadow">
            {{-- Pacman Loader for background image - show only when cover exists --}}
            @if($trade_game->image_cover)
            <div class="loader pacman-loader cover-loader"></div>
            {{-- Show game name, when no cover exist --}}
            @else
            <div class="no-cover-name">{{$trade_game->name}}</div>
            @endif

              <a href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_' . $trade_game->id : '#LoginModal' }}">

                {{-- Start Additional Charge Ribbon --}}
                @if($add_charge[$trade_game->id]['price_type'] != 'none')
                @if($add_charge[$trade_game->id]['price_type'] == 'want')
                <div class="ribbon ribbon-clip ribbon-bottom ribbon-danger">
                @elseif($add_charge[$trade_game->id]['price_type'] == 'give')
                <div class="ribbon ribbon-clip ribbon-bottom ribbon-success">
                @endif
                  <div class="ribbon-inner">
                    @if($add_charge[$trade_game->id]['price_type'] == 'want')
                    <span class="currency"><i class="fa fa-minus"></i></span>
                    @elseif($add_charge[$trade_game->id]['price_type'] == 'give')
                    <span class="currency"><i class="fa fa-plus"></i></span>
                    @endif<span class="price"> {{ money($add_charge[$trade_game->id]['price'], Config::get('settings.currency')) }}</span>
                  </div>
                </div>
                @endif
                {{-- End Additional Charge Ribbon --}}

                {{-- Start Game Cover --}}
                {{-- Generated game cover with platform on top --}}
                @if($trade_game->cover_generator)
                  <div class="lazy game-cover gen"  data-original="{{$trade_game->image_cover}}"></div>
                  <div class="game-platform-gen" style="background-color: {{$trade_game->platform->color}}; text-align: {{$trade_game->platform->cover_position}};">
                    {{-- Check if platform logo setting is enabled --}}
                    @if( config('settings.platform_logo') )
                      <img src="{{ asset('logos/' . $trade_game->platform->acronym . '_tiny.png/') }}" alt="{{$trade_game->platform->name}} Logo">
                    @else
                      <span>{{$trade_game->platform->name}}</span>
                    @endif
                  </div>
                {{-- Normal game cover --}}
                @else
                  <div class="lazy game-cover"  data-original="{{$trade_game->image_cover}}"></div>
                @endif
                {{-- End Game Cover --}}

                @if($trade_game->image_cover)
                <div class="item-name">
                  {{ $trade_game->name }} @if($trade_game->limited_edition)<span><i class="fa fa-star" aria-hidden="true"></i> {{ $trade_game->limited_edition }}<span>@endif
                </div>
                @endif

                {{-- Exchange Icon overlay on hover --}}
                <div class="imgDescription gcover">
                  <div class="valign">
                    <i class="fa fa-exchange" aria-hidden="true"></i>
                  </div>
                </div>

              </a>

          </div>
        </div>

        {{-- Start Trade Modal --}}
        <div class="modal fade modal-fade-in-scale-up modal-trade" id="modal-trade_{{$trade_game->id}}" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">

              <div class="modal-header">
                {{-- Background pattern --}}
                <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
                {{-- Background color --}}
                <div class="background-color"></div>
                {{-- Title (Trade / Close button) --}}
                <div class="title">
                  <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
                  </button>
                  <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-exchange m-r-5" aria-hidden="true"></i>
                    {!! trans('listings.modal_trade.trade_game' , ['game_name' => $listing->game->name]) !!}
                  </h4>
                </div>

              </div>

              <div class="modal-body">
                <div class="flex-center-space">
                  {{-- Start Game Info --}}
                  <div class="game-overview">
                    <div>
                      {{-- Game cover --}}
                      <span class="avatar cover">
                        <img src="{{ $game->image_square_tiny }}">
                      </span>
                    </div>
                    <div>
                      {{-- Game title & platform --}}
                      <span class="title">
                        <strong>{{$game->name}}</strong>
                      </span>
                      <span class="platform" style="background-color:{{$game->platform->color}};">
                        {{$game->platform->name}}
                      </span>
                    </div>
                  </div>
                  {{-- End Game Info --}}
                  {{-- Additional charge from user --}}
                  <div class="additional-charge flex-center">
                    @if($add_charge[$trade_game->id]['price_type'] == 'give')
                    <div class="charge-icon">
                      <i class="fa fa-plus"></i>
                    </div>
                    <div class="charge-money">
                      {{ money($add_charge[$trade_game->id]['price'], Config::get('settings.currency')) }}
                    </div>
                    @endif
                  </div>
                </div>

                <div class="seperator"><span><i class="fa fa-exchange" aria-hidden="true"></i></span></div>

                <div class="game-overview trade game">
                  {{-- Additional charge from Partner --}}
                  <div class="additional-charge flex-center">
                    @if($add_charge[$trade_game->id]['price_type'] == 'want')
                    <div class="charge-money partner">
                      {{ money($add_charge[$trade_game->id]['price'], Config::get('settings.currency')) }}
                    </div>
                    <div class="charge-icon partner">
                      <i class="fa fa-plus"></i>
                    </div>
                    @endif
                  </div>
                  {{-- Start Info from trade game --}}
                  <div class="overview">
                    <div>
                      <span class="title">
                        <strong>{{$trade_game->name}}</strong>
                      </span>
                      <span class="platform" style="background-color:{{$trade_game->platform->color}};">
                        {{$trade_game->platform->name}}
                      </span>
                    </div>
                    <div>
                      <span class="avatar cover trade">
                        <img src="{{$trade_game->image_square_tiny}}">
                      </span>
                    </div>
                  </div>
                  {{-- End Info from trade game --}}
                </div>

              </div>

              <div class="modal-footer">
                {!! Form::open(array('url'=>'offer/add', 'id'=>'form-trade-' . $trade_game->id, 'role'=>'form')) !!}

                {{-- Encrypt hidden inputs. We don't want, that the user can change the values --}}
                <input name="game_id" type="hidden" value="{{  encrypt($game->id) }}">
                <input name="listing_id" type="hidden" value="{{ encrypt($listing->id) }}">
                <input name="trade_game" type="hidden" value="{{ $trade_game->id }}">
                <a href="javascript:void(0)" class="cancel-button" data-dismiss="modal">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ trans('listings.modal.close') }}
                </a>
                <a href="javascript:void(0)" class="trade-button trade-submit" data-trade="{{$trade_game->id}}">
                  <span><i class="fa fa-exchange" aria-hidden="true"></i> {{ trans('listings.general.trade') }}</span>
                </a>
                {!! Form::close() !!}
              </div>

            </div>
          </div>
        </div>
        {{-- End Trade Modal --}}
      @endforeach
      @endif

      {{-- Check if trade suggestions are allowed --}}
      @if($listing->trade_negotiate)
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2">
          {{-- Start Game Cover --}}
          <div class="card game-cover-wrapper hvr-grow-shadow">

            {{-- Cover for trade suggestion --}}
            <div class="game-cover-suggestion " style="background: radial-gradient(rgba(48,47,47,0) 0, rgba(48,47,47,0.7) 60%, rgba(48,47,47,0.9) 100%), url({{ asset('/img/game_pattern_white.png') }}) 0% 20%;"></div>

            {{-- Suggestion icon --}}
            <div class="no-cover-name suggestion-icon"><i class="fa fa-retweet" aria-hidden="true"></i></div>
            {{-- Title (Suggest a Game) --}}
            <div class="suggestion-name m-t-40">{{ trans('listings.modal_trade.suggest') }}</div>

              <a href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#modal-trade_suggestion' : '#LoginModal' }}">

                {{-- Exchange Icon overlay on hover --}}
                <div class="imgDescription gcover">
                  <div class="valign">
                    <i class="fa fa-retweet" aria-hidden="true"></i>
                  </div>
                </div>

              </a>

          </div>
        </div>

        {{-- Start Trade Suggestion Modal --}}
        <div class="modal fade modal-fade-in-scale-up modal-trade" id="modal-trade_suggestion" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">

              <div class="modal-header">
                {{-- Background pattern --}}
                <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
                {{-- Background color --}}
                <div class="background-color"></div>
                {{-- Title (Trade / Close button) --}}
                <div class="title">
                  <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
                  </button>
                  <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-exchange m-r-5" aria-hidden="true"></i>
                    {!! trans('listings.modal_trade.trade_game' , ['game_name' => $listing->game->name]) !!}
                  </h4>
                </div>

              </div>
              {!! Form::open(array('url'=>'offer/add', 'id'=>'form-trade-suggestion', 'role'=>'form')) !!}
              <div class="modal-body">
                <div class="flex-center-space">
                  {{-- Start Game Info --}}
                  <div class="game-overview">
                    <div>
                      {{-- Game cover --}}
                      <span class="avatar cover">
                        <img src="{{ $game->image_square_tiny }}">
                      </span>
                    </div>
                    <div>
                      {{-- Game title & platform --}}
                      <span class="title">
                        <strong>{{$game->name}}</strong>
                      </span>
                      <span class="platform" style="background-color:{{$game->platform->color}};">
                        {{$game->platform->name}}
                      </span>
                    </div>
                  </div>
                  {{-- End Game Info --}}
                  {{-- Additional charge from user --}}
                  <div class="additional-charge contract flex-center" id="add_charge_user_wrapper">
                    <a class="charge-icon" id="add_charge_user_button" href="#">
                      <i class="far fa-money-bill money-user m-r-5"></i><i class="fa fa-plus"></i>
                    </a>
                    <div class="charge-money" id="add_charge_user_form" style="display: none;">
                      <input type="text" name="add_charge_user" id="add_charge_user" placeholder="{{ trans('listings.form.placeholder.sell_price_suggestion',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}" class="form-control input">
                    </div>
                  </div>
                </div>

                <div class="seperator m-b-10"><span><i class="fa fa-exchange" aria-hidden="true"></i></span></div>

                {{-- Input group for game search --}}
                <div id="select-game">
                  <div class="input-group input-group-lg select-game m-b-10">
                    <span class="input-group-addon">
                      {{-- Search icon when search is complete --}}
                      <span id="listingsearchcomplete">
                        <i class="fa fa-search"></i>
                      </span>
                      {{-- Spin icon when search is in progress --}}
                      <span class="hidden" id="listingsearching">
                        <i class="fa fa-sync fa-spin"></i>
                      </span>
                    </span>
                    {{-- Input for typeahead --}}
                    <input type="text" class="form-control rounded input-lg inline input" id="offersearch">
                  </div>
                </div>
                {{-- Selected game --}}
                <div class="selected-game"></div>
              </div>

              <div class="modal-footer">
                {{-- Encrypt hidden inputs. We don't want, that the user can change the values --}}
                <input name="game_id" type="hidden" value="{{  encrypt($game->id) }}">
                <input name="listing_id" type="hidden" value="{{ encrypt($listing->id) }}">
                <a href="javascript:void(0)" class="cancel-button" data-dismiss="modal">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ trans('listings.modal.close') }}
                </a>
                <a href="javascript:void(0)" class="trade-button loading" id="trade-submit-suggestion">
                  <span><i class="fa fa-exchange" aria-hidden="true"></i> {{ trans('listings.general.trade') }}</span>
                </a>
              </div>
              {!! Form::close() !!}
            </div>
          </div>
        </div>
        {{-- End Trade Suggestion Modal --}}
      @endif


      </div>
      {{-- End Trade game list --}}
      {{-- End Listing Trade --}}

      @endif


      <section class="panel">
        {{-- User informations --}}
        <div class="panel-heading flex-center-space-wrap padding">
          <div class="flex-overflow-fix">
            <a class="profile-link flex-center" href="{{$listing->user->url}}">
              {{-- User Avatar --}}
              <span class="avatar @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif m-r-10 no-flex-shrink">
                <img src="{{$listing->user->avatar_square}}" alt="{{$listing->user->name}}'s Avatar"><i></i>
              </span>
              {{-- User Name & Location --}}
              <div class="flex-overflow-fix">
                {{-- User Name --}}
                <span class="profile-name small">
                  {{$listing->user->name}}
                </span>
                {{-- User Location --}}
                <span class="profile-location small">
                {{-- Check if user have set an location --}}
                @if($listing->user->location)
                  {{-- Country flag --}}
                  <img src="{{ asset('img/flags/' .   $listing->user->location->country_abbreviation . '.svg') }}" height="14"/> {{-- Country code--}}{{$listing->user->location->country_abbreviation}}, {{-- Location name --}}{{$listing->user->location->place}} {{-- Postal code --}}<span class="postal-code">{{$listing->user->location->postal_code}}</span>
                @endif
                </span>
              </div>
            </a>
          </div>
          {{-- User Ratings --}}
          <div class="no-flex-shrink">
          @if(is_null($listing->user->positive_percent_ratings))
            {{-- No Ratings --}}
            <span class="fa-stack fa-lg">
              <i class="fa fa-thumbs-up fa-stack-1x"></i>
              <i class="fa fa-ban fa-stack-2x text-danger"></i>
            </span>
            <span class="no-ratings small">{{ trans('users.general.no_ratings') }}</span>
          @else
            @php
              if($listing->user->positive_percent_ratings > 70){
                $rating_icon = 'fa-thumbs-up text-success';
              }else if($listing->user->positive_percent_ratings > 40){
                $rating_icon = 'fa-minus';
              }else{
                $rating_icon = 'fa-thumbs-down text-danger';
              }
            @endphp
            {{-- Ratings in percent --}}
            <span class="rating-percent small"><i class="fa {{$rating_icon}}" aria-hidden="true"></i> {{$listing->user->positive_percent_ratings}}%</span>
            {{-- Ratings Count --}}
            <div class="rating-counts small">
              <span class="text-danger"><i class="fa fa-thumbs-down" aria-hidden="true"></i> {{$listing->user->negative_ratings}}</span>&nbsp;&nbsp;
              <i class="fa fa-minus" aria-hidden="true"></i> {{$listing->user->neutral_ratings}}&nbsp;&nbsp;
              <span class="text-success"><i class="fa fa-thumbs-up" aria-hidden="true"></i> {{$listing->user->positive_ratings}}</span>
            </div>
          @endif
          </div>
        </div>

        @if($listing->user->location && $listing->user->location->latitude && $listing->user->location->longitude && config('settings.google_maps_key'))
        <div class="panel-body n-p">
          {{-- Start google maps wrapper --}}
          <div class="google-maps"></div>
        </div>

        <div class="panel-footer google-maps-info padding">
          {{-- Distance --}}
          <div>
          @if($listing->distance !== false)
            <i class="fa fa-location-arrow" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}</span>
          @endif
          </div>
          {{-- Open location in google maps --}}
          <div>
            <a href="http://maps.google.com/?q={{$listing->user->location->latitude}},{{$listing->user->location->longitude}}" target="_blank"><i class="fab fa-google" aria-hidden="true"></i> {{ trans('listings.general.open_google_maps') }}</a>
          </div>
        </div>
        @endif
      </section>

    </div>
    {{-- End Listings tab --}}

      {{-- Start Media (Images & Videos) tab --}}
      <div class="tab-pane fade" id="media" role="tabpanel">
      </div>
      {{-- End Media (Images & Videos) tab --}}

      {{-- Start Edit / Delete when user has permission --}}
      @if( Auth::check() && ((Auth::user()->id == $listing->user_id) || Auth::user()->can('edit_listings')))
      <div>
        @if($listing->status == 0 || is_null($listing->status))
        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal_delete_{{$listing->id}}" class="btn btn-danger m-r-5"><i class="fa fa-trash"></i> {{ trans('general.delete') }}</a>
        <a href="{{ $listing->url_slug . '/edit' }}" class="btn btn-dark"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>

        {{-- Start modal for delete listing --}}
        <div class="modal fade modal-fade-in-scale-up modal-danger" id="modal_delete_{{$listing->id}}" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
            <div class="modal-content">

              <div class="modal-header">

                <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

                <div class="title">
                  <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
                  </button>
                  {{-- Delete  listing title --}}
                  <h4 class="modal-title" id="myModalLabel">
                    <i class="fa fa-trash m-r-5" aria-hidden="true"></i>{{ trans('users.modal_delete_listing.title', ['gamename' => $listing->game->name]) }}
                  </h4>
                </div>

              </div>

              <div class="modal-body">
                {{-- Delete info --}}
                <span><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('users.modal_delete_listing.info') }}</span>

              </div>

              <div class="modal-footer">
                {!! Form::open(array('url'=>'listings/delete', 'id'=>'form-delete', 'role'=>'form')) !!}
                {{-- Close button --}}
                <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical m-r-10"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span></a>
                <input name="listing_id" type="hidden" value="{{ encrypt($listing->id) }}">
                {{-- Delete button --}}
                <button class="btn btn-lg btn-danger btn-animate btn-animate-vertical" type="submit" id="delete-submit">
                  <span><i class="icon fa fa-trash" aria-hidden="true"></i> {{ trans('users.modal_delete_listing.delete_listing') }}
                  </span>
                </button>
                {!! Form::close() !!}
              </div>
            </div>
          </div>
        </div>
        @endif
        {{-- End modal for delete listing --}}
      </div>
      @endif
      {{-- End Edit / Delete when user has permission --}}

    </div>
    {{-- Start Listings tab --}}



      {{-- Start Buy Modal --}}
      <div class="modal fade modal-fade-in-scale-up modal-buy" id="modal-buy" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">

            <div class="modal-header">
              {{-- Background pattern --}}
              <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
              {{-- Background color --}}
              <div class="background-color"></div>
              {{-- Title (Buy & Close button) --}}
              <div class="title">
                <button type="button" class="close" data-dismiss="modal">
                  <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                  <i class="fa fa-shopping-basket m-r-5" aria-hidden="true"></i>
                  {!! trans('listings.modal_buy.buy_game', ['game_name' => $game->name]) !!}
                </h4>
              </div>

            </div>

            {!! Form::open(array('url'=>'offer/add', 'id'=>'form-buy', 'role'=>'form')) !!}

            <div class="modal-body">
              {{-- Start Game Details --}}
              <div class="game-overview">
                <div>
                  {{-- Game Cover --}}
                  <span class="avatar cover">
                    <img src="{{$game->image_square_tiny}}">
                  </span>
                </div>
                <div>
                  {{-- Game Title & platform --}}
                  <span class="title">
                    <strong>{{$game->name}}</strong>
                  </span>
                  <span class="platform" style="background-color:{{$game->platform->color}};">
                    {{$game->platform->name}}
                  </span>
                </div>
              </div>
              {{-- End Game Details --}}

              <div class="seperator"><span><i class="fa fa-shopping-basket" aria-hidden="true"></i></span></div>
              {{-- Start Buy Overview --}}
              <div class="price-overview row no-space">
                <div class="col-xs-12 col-md-6 row no-space" style="text-align: left;">
                  {{-- Check if seller accept delivery --}}
                  @if($listing->delivery)
                  {{-- Delivery --}}
                  <div class="m-b-10 col-md-12 col-xs-6 {{ $listing->delivery && $listing->pickup ? 'col-xs-6' : 'col-xs-12'}}">
                    {{-- Delivery checkbox --}}
                    <div class="checkbox-offer checkbox-default checkbox-lg {{ $listing->delivery && $listing->pickup ? 'both-options' : ''}}" id="delivery-wrapper">
                      <input type="checkbox" id="delivery" name="delivery" checked {{ $listing->delivery && $listing->pickup ? '' : 'disabled'}}/>
                      {{-- Checkbox label (Delivery) --}}
                      <label for="delivery">
                        <i class="fa fa-truck {{ $listing->delivery && $listing->pickup ? 'hidden-xs-down' : ''}}" aria-hidden="true"></i> {{ trans('listings.general.delivery') }}
                      </label>
                    </div>
                    {{-- Secure Payment --}}
                    @if($listing->payment)
                    <div id="secure-payment" class="m-t-5">
                      <div class="paypal-payment inline-block">
                        {{-- Secure payment text --}}
                        <span class="p-10 inline-block">{{ trans('payment.secure_payment') }}</span><span class="protected"><i class="fa fa-shield-check" aria-hidden="true"></i></span>
                      </div>
                      <div class="payment-gateways">
                          {{-- PayPal --}}
                          @if( config('settings.paypal') )
                              <i class="fab fa-cc-paypal m-r-5" aria-hidden="true"></i>
                          @endif
                          {{-- Stripe --}}
                          @if( config('settings.stripe'))
                              <i class="fab fa-cc-stripe m-r-5" aria-hidden="true"></i>
                          @endif
                      </div>
                    </div>
                    @else
                      <div id="secure-payment" class="m-t-5">
                        <div class="paypal-payment cash inline-block">
                          {{-- unsecure payment text --}}
                          <span class="p-10 inline-block">{{ trans('payment.unsecure_payment') }}</span><span class="p-10 inline-block protected hidden-xs-down"><i class="fa fa-times-octagon" aria-hidden="true"></i></span>
                        </div>
                      </div>
                    @endif
                  </div>
                  @endif
                  {{-- Check if seller accept pickup --}}
                  @if($listing->pickup)
                  <div class="col-md-12 col-xs-6 {{ $listing->delivery && $listing->pickup ? 'col-xs-6' : 'col-xs-12'}}">
                    {{-- Pickup checkbox --}}
                    <div class="checkbox-offer checkbox-default checkbox-lg m-b-5 {{ $listing->delivery && $listing->pickup ? 'both-options unchecked' : ''}}" id="pickup-wrapper">
                      <input type="checkbox" id="pickup" name="pickup" {{ $listing->delivery && $listing->pickup ? '' : 'checked disabled'}} />
                      {{-- Checkbox label (Pickup) --}}
                      <label for="pickup">
                        <i class="far fa-handshake {{ $listing->delivery && $listing->pickup ? 'hidden-xs-down' : ''}}" aria-hidden="true"></i> {{ trans('listings.general.pickup') }}
                      </label>
                    </div>
                    {{-- Cash payment --}}
                    <div id="cash-payment" class="{{ $listing->delivery && $listing->pickup ? 'hidden' : ''}}">
                      <div class="paypal-payment cash inline-block">
                        {{-- Cash payment text and icon --}}
                        <span class="p-10 inline-block">{{ trans('payment.cash_payment') }}</span><span class="p-10 inline-block protected hidden-xs-down"><i class="far fa-money-bill-alt m-r-5" aria-hidden="true"></i></span>
                      </div>
                    </div>
                  </div>
                  @endif
                </div>
                <div class="col-xs-12 col-md-6">
                  <span class="total">
                    {{ trans('listings.modal_buy.total') }}
                  </span>
                  {{-- Check if price suggestions are allowed --}}
                  @if($listing->sell_negotiate)
                    {{-- Listing Price --}}
                    <span id="listing-price">{{ $listing->getPrice() }}</span>
                    {{-- Price suggestion link --}}
                    <div id="price-suggest-link-wrapper">
                      <a href="#" id="price-suggest-link" class="price-suggest-link btn btn-dark"><i class="fa fa-retweet" aria-hidden="true"></i> {{ trans('listings.modal_buy.suggest_price') }}</a>
                    </div>
                    {{-- Price suggestion form --}}
                    <div id="price-suggest-form" class="flex-center-space" style="display: none;">
                      <div></div>
                      <div  class="input-group input-group-lg" style="width: 200px; font-weight: 500 !important;">
                        <span class="input-group-addon">
                          <span>{{ Currency(Config::get('settings.currency'))->getSymbol() }}</span>
                        </span>
                        {{-- Price Input --}}
                        <input type="text" class="form-control rounded input-lg inline input" style="text-align: right;"
                        data-validation="number,required" data-validation-ignore=",,." data-validation-error-msg='<div class="alert dark alert-icon alert-danger" role="alert"><i class="icon fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans('listings.form.validation.price') }}</div>' data-validation-error-msg-container="#price-error-dialog" name="price_suggestion" id="price_suggestion" autocomplete="off" value="{{(isset($listing) ? old('price',$listing->getPrice(false)) : null)}}" placeholder="{{ trans('listings.form.placeholder.sell_price_suggestion',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}"/>
                      </div>
                    </div>
                  @else
                    {{-- Listing Price --}}
                    {{ $listing->getPrice() }}
                  @endif
                  {{-- Delivery Price --}}
                  @if($listing->delivery)
                  <span class="total shipping {{ $listing->sell_negotiate ? 'm-t-5' : ''}}" id="total-shipping">
                    @if(is_null($listing->delivery_price) || $listing->delivery_price == 0 )
                      {{ trans('listings.modal_buy.delivery_free') }}
                    @else
                      {{ trans('listings.modal_buy.delivery_price', ['price' => $listing->getDeliveryPrice()]) }}
                    @endif
                  </span>
                  @endif
                </div>
              </div>
              {{-- End Buy Overview --}}
            </div>

            <div class="modal-footer">
              {{-- Encrypt hidden inputs. We don't want, that the user can change the values --}}
              <input name="game_id" type="hidden" value="{{  encrypt($game->id) }}">
              <input name="listing_id" type="hidden" value="{{ encrypt($listing->id) }}">
              <a href="javascript:void(0)" class="cancel-button" data-dismiss="modal">
                <i class="fa fa-times" aria-hidden="true"></i> {{ trans('listings.modal.close') }}
              </button>
              <a href="javascript:void(0)" class="buy-button" id="buy-submit">
                <span><i class="fa fa-shopping-basket" aria-hidden="true"></i> {{ trans('listings.modal_buy.buy') }}</span>
              </a>
            </div>

            {!! Form::close() !!}

          </div>
        </div>
      </div>
      {{-- End Buy Modal --}}

    </div>
  </div>
  {{-- End Item Content --}}
</div>


{{-- Include new message modal --}}
{{-- Check if logged in user is listing user --}}
@if(!(Auth::check() && Auth::user()->id == $listing->user_id))
  @include('frontend.messenger.partials.modal-message', ['user' => $listing->user])
@endif

{{-- Include modal for wishlist --}}
@include('frontend.wishlist.inc.modal-wishlist')

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('listing', $listing) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>

{{-- Load comment script --}}
@if(config('settings.comment_listing'))
  @yield('comments-script')
@endif

{{-- Start Mustache Template for selected game --}}
<script id="selected-game" type="x-tmpl-mustache">
  <div>
    <div class="flex-center-space">
      {{-- Additional charge from partner --}}
      <div class="additional-charge contract flex-center" id="add_charge_partner_wrapper">
        <div class="charge-money partner" id="add_charge_partner_form" style="display: none;">
          <input type="text" name="add_charge_partner" id="add_charge_partner" placeholder="{{ trans('listings.form.placeholder.sell_price_suggestion',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}" class="form-control input">
        </div>
        <a class="charge-icon partner" id="add_charge_partner_button" href="#">
          <i class="fa fa-plus"></i><i class="far fa-money-bill money-partner m-l-5"></i>
        </a>
      </div>
      <div class="game-overview trade game">
        <div>
        </div>
        {{-- Start Info from trade game --}}
        <div class="overview">
          <div>
            <span class="title">
              <strong><% name %></strong>
            </span>
            <span class="platform" style="background-color:<% platform_color %>;">
              <% platform_name %>
            </span>
          </div>
          <div>
            <span class="avatar cover trade">
              <img src="<% pic %>">
            </span>
          </div>
        </div>
        {{-- End Info from trade game --}}
      </div>
    </div>
  <input name="trade_game" type="hidden" value="<% id %>">

  <div class="flex-center-space m-t-20">
    <div></div>
    <div><a href="javascript:void(0)" id="reselect-game" class="btn btn-dark"><i class="fa fa-repeat" aria-hidden="true"></i> {{ trans('listings.form.game.reselect') }}</a></div>
  </div>
</div>
</script>
{{-- End Mustache Template for selected game --}}


{{-- Load mask money script if price suggestions is activated --}}
@if($listing->sell_negotiate || $listing->trade_negotiate)
<script src="{{ asset('js/autoNumeric.min.js') }}"></script>
@endif

{{-- Load google maps when user location have lat and long --}}
@if($listing->user->location && $listing->user->location->latitude && $listing->user->location->longitude && config('settings.google_maps_key'))
<script src="//cdnjs.cloudflare.com/ajax/libs/gmap3/7.2.0/gmap3.min.js"></script>
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={{ config('settings.google_maps_key') }}"></script>
@endif

@if(isset($listing->images))
<link rel="stylesheet" href="{{ asset('css/magnific-popup.min.css') }}">
@endif

<script type="text/javascript">
$(document).ready(function(){


   /*$.ajax({
   url : 'http://localhost/wiledia2/public/translate/en/de/{{$listing->game->description}}',
   type: 'GET',

     success: function(data){
         $('.gsummary').html(data);
     }
  });*/

{{-- Delivery / pickup toggle - only if both is accepted --}}
@if($listing->delivery && $listing->pickup)
  var delivery = $('#delivery');
  var pickup = $('#pickup');

  delivery.change(function() {
    toggleDelivery(this.checked);
  });

  pickup.change(function() {
    toggleDelivery(this.checked ? false : true);
  });

  function toggleDelivery(deliveryChecked){
    if (deliveryChecked) {
      delivery.prop('checked', true);
      $('#delivery-wrapper').toggleClass('unchecked');
      pickup.prop('checked', false).prev('.checkbox-offer').toggleClass('unchecked');
      $('#pickup-wrapper').toggleClass('unchecked');
      $('#secure-payment').slideDown(300);
      $('#cash-payment').slideUp(300);
      @if($listing->delivery)
        $('#total-shipping').slideDown(300);
      @endif
    } else {
      delivery.prop('checked', false);
      $('#delivery-wrapper').toggleClass('unchecked');
      pickup.prop('checked', true);
      $('#pickup-wrapper').toggleClass('unchecked');
      $('#secure-payment').slideUp(300);
      $('#cash-payment').slideDown(300);
      @if($listing->delivery)
        $('#total-shipping').slideUp(300);
        $('#cash-payment').slideDown(300);
      @endif
    }
  }
@endif

@if(isset($listing->images))
  {{-- Popup for picture --}}
  $('.listing-image-wrapper').magnificPopup({
      type: 'image',
      tClose: '{{ trans('games.gallery.close') }}',
      tLoading: '{{ trans('games.gallery.loading') }}',
      @if(count($listing->images)>1)
      gallery: {
        tPrev: '{{ trans('games.gallery.prev') }}',
        tNext: '{{ trans('games.gallery.next') }}',
        tCounter: '%curr% {{ trans('games.gallery.counter') }} %total%',
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
      },
      @endif
      image: {
          tError: '{{ trans('games.gallery.error') }}'
      },
      mainClass: 'mfp-zoom-in',
      removalDelay: 300, //delay removal by X to allow out-animation
      callbacks: {
          beforeOpen: function() {
              $('#portfolio a').each(function(){
                  $(this).attr('title', $(this).find('img').attr('alt'));
              });
          },
          open: function() {
              //overwrite default prev + next function. Add timeout for css3 crossfade animation
              $.magnificPopup.instance.next = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.next.call(self); }, 120);
              };
              $.magnificPopup.instance.prev = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.prev.call(self); }, 120);
              };
          },
          imageLoadComplete: function() {
              var self = this;
              setTimeout(function() { self.wrap.addClass('mfp-image-loaded'); }, 16);
          }
      }
   });
@endif

  {{-- Google maps when user location have lat and long --}}
  @if($listing->user->location && $listing->user->location->latitude && $listing->user->location->longitude && config('settings.google_maps_key'))
  var center = [{{$listing->user->location->latitude}}, {{$listing->user->location->longitude}}];
  $('.google-maps')
    .gmap3({
      center: center,
      zoom: 11,
      mapTypeId : google.maps.MapTypeId.ROADMAP,
      zoomControl: true,
      scaleControl: true,
      streetViewControl: true,
      fullscreenControl: true,
      mapTypeControl: true
    })
    .marker([
      {position:[{{$listing->user->location->latitude}}, {{$listing->user->location->longitude}}]}
    ])
    .circle({
      center: center,
      radius : 2000,
      fillColor : "#FFAF9F",
      strokeWeight : 0
    });

  @endif

  @if($listing->sell_negotiate)
  {{-- Start mask prices for money input --}}
  var price_suggestion = $("#price_suggestion");
  var price_suggestion_link = $("#price-suggest-link");
  var price_suggestion_form = $("#price-suggest-form");

  {{-- Start mask prices for money input --}}
  const autoNumericOptions = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
  };

  price_suggestion.autoNumeric('init', autoNumericOptions);

  price_suggestion_link.click( function() {
    $("#listing-price").addClass('price-suggestion-enabled ');
    $('#price-suggest-link-wrapper').css({opacity: 0, transition: 'opacity 0.4s'}).slideUp();
    price_suggestion_form.slideDown();
    return false;
  })
  @endif


  @if($listing->trade_negotiate)


  {{-- Start mask prices for money input --}}
  var add_charge_user = $("#add_charge_user");

  {{-- Start mask prices for money input with currency symbol --}}
  const autoNumericOptionsSymbol = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
      currencySymbol             : '{{ Currency(Config::get('settings.currency'))->getSymbol() }}{{ Currency(Config::get('settings.currency'))->isSymbolFirst() ? ' ' : '' }}',
      currencySymbolPlacement    : '{{ Currency(Config::get('settings.currency'))->isSymbolFirst() ? 'p' : 's' }}',
  };

  add_charge_user.autoNumeric('init', autoNumericOptionsSymbol);



  $("#add_charge_user_button").click(function () {
    if ( !$('#add_charge_partner_wrapper').hasClass('contract') ) {
      $("#add_charge_partner").val('');
      $('#add_charge_partner_wrapper').toggleClass('contract');
      $('#add_charge_partner_form').animate({width:'toggle'},100);
    }
    add_charge_user.val('');
    add_charge_user.autoNumeric('init', autoNumericOptionsSymbol);
    $('#add_charge_user_wrapper').toggleClass('contract');
    $('#add_charge_user_form').animate({width:'toggle'},100);
    return false;
  });

  {{-- Submit button --}}
  var trade_submit_suggestion = $("#trade-submit-suggestion");

  {{-- Start typeahead for listing game search --}}
  {{-- Bloodhound engine with remote search data in json format --}}
  $('#offersearch').submit(false);
  var listingGameSearch = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: '{{ url("games/search/json/%QUERY") }}',
      wildcard: '%QUERY'
    }
  });

  {{-- Typeahead with data from bloodhound engine --}}
  $('#offersearch').typeahead(null, {
    name: 'offer-search',
    display: 'name',
    source: listingGameSearch,
    highlight: true,
    limit:6,
    templates: {
      empty: [
        '<div class="nosearchresult bg-danger"><a href="{{ url("games/add") }}">',
          '<span><i class="fa fa-ban"></i> {{ trans('listings.form.validation.no_game_found') }} <strong>{{ trans('listings.form.validation.no_game_found_add') }}</strong><span>',
        '</a></div>'
      ].join('\n'),
      suggestion: function (data) {
          return '<div class="searchresult hvr-grow-shadow2"><span class="link"><div class="inline-block m-r-10"><span class="avatar"><img src="' + data.pic + '" class="img-circle"></span></div><div class="inline-block"><strong class="title">' + data.name + '</strong><br><small class="text-uc text-xs"><span class="platform-label" style="background-color: ' + data.platform_color + ';">' + data.platform_name + '</span></small></div></span></div>';
      }
    }
  })
  .on('typeahead:asyncrequest', function() {
      $('#listingsearchcomplete').hide();
      $('#listingsearching').show();
  })
  .on('typeahead:asynccancel typeahead:asyncreceive', function() {
      $('#listingsearching').hide();
      $('#listingsearchcomplete').show();
  });

  {{-- Change selected game on selecting typeahead --}}
  $('#offersearch').bind('typeahead:selected', function(obj, datum, name) {
    trade_submit_suggestion.removeClass('loading');
    var customTags = [ '<%', '%>' ];
    Mustache.tags = customTags;
    var template = $('#selected-game').html();
    Mustache.parse(template);   // optional, speeds up future uses
    var append_date = Mustache.render(template, datum);
    $('#select-game').slideUp(300);
    $(append_date).hide().appendTo('.selected-game').css({opacity: 1, transition: 'opacity 0.4s'}).slideDown(300);
    $('.listing-form').delay(300).css({opacity: 0, transition: 'opacity 0.4s'}).slideDown(300);
    setTimeout(function(){$('#offersearch').typeahead('val', ''); }, 10);
    $("#add_charge_partner").autoNumeric('init', autoNumericOptionsSymbol);
    $("#add_charge_partner_button").click(function () {
      if ( !$('#add_charge_user_wrapper').hasClass('contract') ) {
        $("#add_charge_user").val('');
        $('#add_charge_user_wrapper').toggleClass('contract');
        $('#add_charge_user_form').animate({width:'toggle'},100);
      }
      $("#add_charge_partner").val('').autoNumeric('init', autoNumericOptionsSymbol);
      $('#add_charge_partner_wrapper').toggleClass('contract');
      $('#add_charge_partner_form').animate({width:'toggle'},100);
      return false;
    });
  });

  {{-- Reset game --}}
  $('.selected-game').on('click', '#reselect-game', function(e) {
    trade_submit_suggestion.addClass('loading');
    e.preventDefault();
    $(this).parent().parent().parent().css({opacity: 0, transition: 'opacity 0.4s'}).slideUp(300, function() {
        $(this).remove();
    });
    $('#select-game').css({opacity: 1, transition: 'opacity 0.4s'}).slideDown(300);
  });
  {{-- End typeahead for listing game search --}}

  {{-- Trade suggestion submit --}}
  trade_submit_suggestion.click( function(){
    $("#trade-submit-suggestion span").html('<i class="fa fa-spinner fa-pulse fa-fw"></i>').addClass('loading');
    $('#form-trade-suggestion').submit();
  });
  @endif



  {{-- Buy submit --}}
  $("#buy-submit").click( function(){
    $('#buy-submit span').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#buy-submit').addClass('loading');
    $('#form-buy').submit();
  });

  {{-- Trade submit --}}
  $(".trade-submit").click( function(){
    $('.trade-submit span').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('.trade-submit').addClass('loading');
    $('#form-trade-' + $(this).data('trade')).submit();
  });

  {{-- Trade Button scroll --}}
  $('#trade-button-subheader').click(function(){
      $('html, body').animate({
          scrollTop: $('#trade-list').offset().top - 20
      }, 500);
      $('#trade-info').fadeIn(500);
      return false;
  });

  {{-- Trade Button scroll on mobile devices --}}
  $('#trade-button-subheader-mobile').click(function(){
      $('html, body').animate({
          scrollTop: $('#trade-list').offset().top - 100
      }, 500);
      $('#trade-info').fadeIn(500);
      return false;
  });

  {{-- Javascript to enable link to tab --}}
  var hash = document.location.hash;
  var prefix = "!";
  if (hash) {
      hash = hash.replace(prefix,'');
      var hashPieces = hash.split('?');
      activeTab = $('[role="tablist"] [data-target="' + hashPieces[0] + '"]');
      activeTab && activeTab.tab('show');

      var $this = activeTab,
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');


      if( !$.trim( $(targ).html() ).length ){

        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }

  }else{
      activeTab = $('[role="tablist"] [data-target="#details"]');
      activeTab && activeTab.tab('show');
  }

  {{-- Change hash for page-reload --}}
  $('[role="tablist"] a').on('shown.bs.tab', function (e) {
      var $this = $(this),
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');


      if( !$.trim( $(targ).html() ).length ){


        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }


      window.location.hash = targ.replace("#", "#" + prefix);


  });
});
</script>
@endsection



@stop
