@extends(Theme::getLayout())

@section('subheader')
  <div class="offer-subheader">
    {{-- Background with game covers --}}
    @if(isset($trade_game))
    <div class="page-hero-bg-img-sell-overview" style="background: linear-gradient(90deg, rgba(26,24,24,0) 0, rgba(26,24,24,0) 20%, rgba(26,24,24,1) 50%, rgba(26,24,24,0) 80%, rgba(26,24,24,0) 100%), url('{{$game->image_cover}}') left 30% no-repeat, url('{{$trade_game->image_cover}}') right 30% no-repeat; background-size: cover, 50%, 50%;  "></div>
    @else
    <div class="page-hero-bg-img-sell-overview" style="background: linear-gradient(90deg, rgba(26,24,24,0) 0, rgba(26,24,24,0) 20%, rgba(26,24,24,1) 50%, rgba(26,24,24,0) 80%, rgba(26,24,24,0) 100%), url('{{$game->image_cover}}') left 30% no-repeat, url() right 30% no-repeat; background-size: cover, 50%, 50%;"></div>
    @endif
    {{-- Background color --}}
    <div class="page-hero-offer-overview-color "></div>

    {{-- Start Offer Subheader content --}}
    <div class="offer-content">
      {{-- Start Offer Overview (Trade or Sell details) --}}
      <div class="offer-overview row no-space">

        <div class="offset-lg-1 col-xs-12 col-lg-2">
          {{-- Start Game Cover --}}
          <div class="game-cover-wrapper hvr-grow-shadow">
            {{-- Pacman Loader for background image - show only when cover exists --}}
            @if($game->image_cover)
            <div class="loader pacman-loader cover-loader"></div>
            {{-- Show game name, when no cover exist --}}
            @else
            <div class="no-cover-name">{{$game->name}}</div>
            @endif

            @if(isset($trade_game) && !is_null($offer->additional_type) && ($offer->additional_type == 'give'))
            {{-- Start Additional Charge Ribbon --}}
            <div class="ribbon ribbon-clip ribbon-bottom {{ Auth::user()->id == $listing->user_id ? 'ribbon-danger' : 'ribbon-success'}}">
              <div class="ribbon-inner">
                <span class="currency"><i class="fa fa-plus"></i></span>
                <span class="price"> {{ money($offer->additional_charge, Config::get('settings.currency')) }}</span>
              </div>
            </div>
            {{-- End Additional Charge Ribbon --}}
            @endif

            {{-- Digital download icon --}}
            @if($listing->digital)
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

            {{-- Item name --}}
            @if($listing->game->image_cover)
            <div class="item-name">
              {{ $listing->game->name }} @if($listing->limited_edition)<span><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>@endif
            </div>
            @elseif($listing->limited_edition)
            <div class="item-name">
              <i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>
            </div>
            @endif

          </div>
          {{-- End Game Cover --}}
        </div>

        {{-- Icon between games or price --}}
        <div class="icon-between col-xs-12 col-lg-4">
            <i class="fa fa-exchange" aria-hidden="true"></i>
            {{-- Show suggestion icon --}}
            @if(!$offer->trade_from_list && !$offer->price_offer || (($offer->price_offer != $listing->price) && !$offer->trade_from_list ))
              <br /><i class="fa fa-retweet" aria-hidden="true"></i>
            @endif
            @if($offer->delivery)
              <br /><i class="fa fa-truck" aria-hidden="true"></i>
              <br />{{ trans('listings.general.delivery') }}
            @else
              <br /><i class="fa fa-handshake" aria-hidden="true"></i>
              <br />{{ trans('listings.general.pickup') }}
            @endif
        </div>

        <div class="col-xs-12 col-lg-2">
          @if(isset($trade_game))
            {{-- Start Trade Game Cover --}}
            <div class="game-cover-wrapper hvr-grow-shadow">
              {{-- Pacman Loader for background image - show only when cover exists --}}
              @if($trade_game->image_cover)
              <div class="loader pacman-loader cover-loader"></div>
              {{-- Show game name, when no cover exist --}}
              @else
              <div class="no-cover-name">{{$trade_game->name}}</div>
              @endif

              @if(isset($trade_game) && !is_null($offer->additional_type) && ($offer->additional_type == 'want'))
              {{-- Start Additional Charge Ribbon --}}
              <div class="ribbon ribbon-clip ribbon-bottom {{ Auth::user()->id == $offer->user_id ? 'ribbon-danger' : 'ribbon-success'}}">
                <div class="ribbon-inner">
                  <span class="currency"><i class="fa fa-plus"></i></span>
                  <span class="price"> {{ money($offer->additional_charge, Config::get('settings.currency')) }}</span>
                </div>
              </div>
              {{-- End Additional Charge Ribbon --}}
              @endif

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

              {{-- Item name --}}
              @if($trade_game->image_cover)
              <div class="item-name">
                {{ $trade_game->name }}
              </div>
              @endif

            </div>
            {{-- End Trade Game Cover --}}
          @else
            {{-- Price --}}
            @if($offer->price_offer != $listing->price)
              {{-- Listing price --}}
              <div class="listing-price">{{$listing->price_formatted}}</div>
            @endif
            {{-- Offer price --}}
            <span class="offer-price">{{$offer->price_offer_formatted}}</span>
            {{-- Difference calculation --}}
            @if($offer->price_offer != $listing->price)
              @if($listing->price != 0 && $offer->price_offer != 0 && $offer->price_offer < $listing->price)
                @php $perc = abs(round(($offer->price_offer / $listing->price) * 100 - 100)); @endphp
                <div class="price-difference text-danger"><strong>- {{ money($listing->price - $offer->price_offer, Config::get('settings.currency'))->format(true) }}</strong><i class="fa fa-caret-down m-l-10" aria-hidden="true"></i> {{$perc}}%</div>
                @elseif($listing->price != 0 && $offer->price_offer != 0)
                @php $perc = round(($offer->price_offer / $listing->price) * 100 - 100); @endphp
                <div class="price-difference text-success"><strong>+ {{ money($offer->price_offer - $listing->price, Config::get('settings.currency'))->format(true) }}</strong><i class="fa fa-caret-up m-l-10" aria-hidden="true"></i> {{$perc}}%</div>
              @endif
            @endif
            {{-- Delivery price --}}
            @if($listing->delivery && $offer->delivery && $listing->delivery_price != '0')
              <span class="delivery-price">{{ trans('listings.modal_buy.delivery_price', ['price' => $listing->getDeliveryPrice()]) }}</span>
            @endif
          @endif
        </div>

      </div>
      {{-- End Offer Overview (Trade or Sell details) --}}

  {{-- Check if offer was declined --}}
  @if($offer->declined)
    {{-- Start declined status --}}
    <div class="offer-status bg-danger">

      <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
      <div class="background-color"></div>

      <div class="flex-center-space offer-status-wrapper">
        <div>
          {{-- Decline reason head--}}
          <span class="text-rating">{{ trans('offers.general.decline_reason') }}</span>
          {{-- Decline notice --}}
          @if($offer->decline_note)
            <span class="text">@lang($offer->decline_note)</span>
          @else
            <span class="text">{{ trans('offers.general.decline_reason_empty') }}</span>
          @endif
        </div>
        <div>
          {{-- Icon --}}
          <div class="notification-circle bg-danger inline-block">
            <i class="icon fa fa-times" aria-hidden="true"></i>
          </div>
        </div>

      </div>

    </div>
    {{-- End declined status --}}
  @else
    {{-- Start Status 0 --}}
    {{-- Listing user need to accept the offer --}}
    @if($offer->status == 0)
    <div class="offer-status wait-status">
      <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
      <div class="background-color"></div>

      <div class="flex-center-space offer-status-wrapper">
        {{-- Offer user --}}
        @if(Auth::user()->id == $offer->user_id)
        {{-- Waiting text --}}
        <div class="text">
          {{ trans('offers.status_wait.wait') }}
        </div>
        {{-- Listing user (can accept offer) --}}
        @elseif(Auth::user()->id == $listing->user_id)
        <div>
          {{-- Accept button --}}
          <a href="#" data-toggle="modal" data-target="#modal_accept" aria-expanded="false" class="btn btn-lg btn-success border-radius">
            <i class="fa fa-check" aria-hidden="true"></i> {{ trans('offers.status_wait.accept') }}
          </a>
          {{-- Decline button --}}
          <a href="#" data-toggle="modal" data-target="#modal_decline" aria-expanded="false" class="btn btn-lg btn-danger border-radius">
            <i class="fa fa-times" aria-hidden="true"></i> {{ trans('offers.status_wait.decline') }}
          </a>
        </div>
        @endif
        {{-- Start status icons --}}
        <div>
          <div class="notification-circle inline-block">
            <i class="fa fa-hourglass" aria-hidden="true"></i>
          </div>

          <span class="hidden-xs-down">

            &nbsp;<i class="fa fa-arrow-right opacity" aria-hidden="true"></i>&nbsp;

            <div class="notification-circle inline-block opacity">
              <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
            </div>

            &nbsp;<i class="fa fa-arrow-right opacity" aria-hidden="true"></i>&nbsp;

            <div class="notification-circle inline-block opacity">
              <i class="icon fa fa-thumbs-up" aria-hidden="true"></i>
            </div>
          </span>

        </div>
        {{-- End status icons --}}
      </div>
    </div>
    @endif
    {{-- End Status 0 --}}

    {{-- Status Status 1 --}}
    {{-- Process sell or trade and rate user --}}
    @if($offer->status == 1)
    <div class="offer-status rate-status">

      <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
      <div class="background-color"></div>

      <div class="flex-center-space offer-status-wrapper">
        {{-- Waiting for payment status --}}
        @if(!$offer->payment && $offer->delivery && $listing->payment && !isset($trade_game))
          <div class="text">{{ trans('payment.offer.awaiting_payment') }}</div>
        @else
          {{-- Rate Buttons for offer user --}}
          @if(Auth::user()->id == $offer->user_id)
            @if(is_null($offer->rating_id_offer))
            <div>
              {{-- Rate user button --}}
              <a href="#" data-toggle="modal" data-target="#modal_close_offer" aria-expanded="false" class="btn btn-lg btn-primary">
                <i class="fa fa-thumbs-up" aria-hidden="true"></i> {{ trans('offers.status_rate.rate_user', ['username' => $listing->user->name]) }}
              </a>
            </div>
            {{-- Wait for rating from listing user --}}
            @elseif(is_null($offer->rating_id_listing))
              <div class="text">
                {{ trans('offers.status_rate.rate_wait', ['username' => $listing->user->name]) }}
              </div>
            @endif
          {{-- Rate Buttons for listing user --}}
          @elseif(Auth::user()->id == $listing->user_id)
            @if(is_null($offer->rating_id_listing))
            <div>
              {{-- Rate user button --}}
              <a href="#" data-toggle="modal" data-target="#modal_close_offer" aria-expanded="false" class="btn btn-lg btn-primary">
                <i class="fa fa-thumbs-up" aria-hidden="true"></i> {{ trans('offers.status_rate.rate_user', ['username' => $offer->user->name]) }}
              </a>
            </div>
            {{-- Wait for rating from offer user --}}
            @elseif(is_null($offer->rating_id_offer))
              <div class="text">
                {{ trans('offers.status_rate.rate_wait', ['username' => $offer->user->name]) }}
              </div>
            @endif
          @endif
        @endif
        <div>
          <span class="hidden-xs-down">
            <div class="notification-circle complete inline-block">
              <i class="fa fa-check" aria-hidden="true"></i>
            </div>
            {{-- Status icons for listing user --}}
            @if(Auth::user()->id == $listing->user_id && !is_null($offer->rating_id_listing))
              &nbsp;<i class="fa fa-arrow-right complete" aria-hidden="true"></i>&nbsp;
              <div class="notification-circle complete inline-block">
                <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
              </div>
            @elseif(Auth::user()->id == $listing->user_id)
              &nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;
              <div class="notification-circle inline-block">
                <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
              </div>
            @endif
            {{-- Status icons for offer user --}}
            @if(Auth::user()->id == $offer->user_id && !is_null($offer->rating_id_offer))
              &nbsp;<i class="fa fa-arrow-right complete" aria-hidden="true"></i>&nbsp;
              <div class="notification-circle complete inline-block">
                <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
              </div>
            @elseif(Auth::user()->id == $offer->user_id)
              &nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp;
              <div class="notification-circle inline-block">
                <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
              </div>
            @endif
            &nbsp;<i class="fa fa-arrow-right opacity" aria-hidden="true" ></i>&nbsp;
          </span>

          <div class="notification-circle inline-block opacity">
            <i class="icon fa fa-thumbs-up" aria-hidden="true"></i>
          </div>

        </div>

      </div>

    </div>
    @endif
    {{-- End Status 1 --}}

    {{-- Start Status 2 --}}
    {{-- Offer complete! show ratings --}}
    @if($offer->status == 2 && (Auth::user()->id == $offer->user_id || Auth::user()->id == $listing->user_id))
    @php
    if(Auth::user()->id == $offer->user_id) {
      $rating = \App\Models\User_Rating::find($offer->rating_id_listing);
    }elseif(Auth::user()->id == $listing->user_id) {
      $rating = \App\Models\User_Rating::find($offer->rating_id_offer);
    }

    $user_from = \App\Models\User::find($rating->user_id_from);
    // Get rating color und icon
    switch ($rating->rating) {
        case 0:
            $rating->icon = "fa-thumbs-down";
            $rating->color = "#c33333";
            break;
        case 1:
            $rating->icon = "fa-minus";
            $rating->color = "#5f5f5f";
            break;
        case 2:
            $rating->icon = "fa-thumbs-up";
            $rating->color = "#519a31";
            break;
    }

    @endphp

    <div class="offer-status" style="background-color: {{$rating->color}};">

      {{-- Revoked overlay --}}
      @if(!$rating->active && $offer->status == 2)
        <div class="declined flex-center">
          <span class="declined-text"><i class="fa fa-repeat"></i> {{ trans('offers.general.revoked') }}</span>
        </div>
      @endif

      <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
      <div class="background-color"></div>

      <div class="flex-center-space offer-status-wrapper">
        <div>
          {{-- Rating from --}}
          <span class="text-rating">{{ trans('offers.status_complete.rating_user', ['username' => $user_from->name]) }}</span>
          {{-- Rating notice --}}
          @if($rating->notice)
            {{-- Head text with username from rater--}}
            <span class="text"><i class="fa fa-quote-left" aria-hidden="true"></i> {{$rating->notice}} <i class="fa fa-quote-right" aria-hidden="true"></i></span>
          @else
            {{-- No notice --}}
            <span class="text">{{ trans('offers.status_complete.no_notice') }}</span>
          @endif
        </div>
        <div>
          {{-- Status icons --}}
            <span class="hidden-xs-down">
            <div class="notification-circle complete inline-block">
              <i class="fa fa-check" aria-hidden="true"></i>
            </div>

            &nbsp;<i class="fa fa-arrow-right complete" aria-hidden="true"></i>&nbsp;

            <div class="notification-circle complete inline-block">
              <i class="icon fa fa-shopping-basket" aria-hidden="true"></i>
            </div>

            &nbsp;<i class="fa fa-arrow-right" aria-hidden="true" style="color: {{$rating->color}};"></i>&nbsp;
          </span>

          <div class="notification-circle inline-block" style="background-color: {{$rating->color}}">
            <i class="icon fa {{$rating->icon}}" aria-hidden="true"></i>
          </div>

        </div>

      </div>

    </div>
    @endif
    {{-- End Status 2 --}}
  @endif

  </div>
  {{-- End Offer Subheader content --}}

</div>
@stop

@section('content')

@if($offer->status >= 1 && $offer->delivery && (($listing->payment && config('settings.payment') && !isset($trade_game)) || $offer->payment))
  <div class="payment-system-form show-fees m-b-20">
    <div class="flex-center-space p-10">
      {{-- Payment System head --}}
      <div class="payment-head" >
        <i class="fa fa-shield"></i> {{ trans('payment.secure_payment') }}
      </div>
      <div>
      </div>
    </div>
    {{-- Paymen status --}}
    <div class="payment-fees flex-center-space">
      <div>
        @if($offer->payment)
          {{-- Money received --}}
          <span class="total-amount">{{ money(abs(filter_var(number_format($offer->payment->total,2), FILTER_SANITIZE_NUMBER_INT)), $offer->payment->currency)->format(true) }}</span>
          <span class="text-light">{{ trans('payment.offer.money_received', ['username' => $offer->payment->user->name]) }}</span>
        @else
          @if(Auth::user()->id == $offer->user_id)
            {{-- Amount to pay --}}
            <div class="flex-center">
                <div class="m-r-10">
                    <span class="total-amount inline-block">{{ $offer->price_offer != $listing->price ? $offer->price_offer_formatted : $listing->price_formatted}}</span> <br>
                    @if(is_null($listing->delivery_price) || $listing->delivery_price == 0 )
                        <span class="text-light"><i class="fa fa-truck"></i> {{ trans('listings.modal_buy.delivery_free') }}</span>
                    @else
                         <span class="text-light"><i class="fa fa-truck"></i> {{ trans('listings.modal_buy.delivery_price', ['price' => $listing->getDeliveryPrice()]) }}</span>
                    @endif
                </div>
                <div>
                {{-- Check if user have enough balance to pay this item--}}
                @if(abs(filter_var(number_format( Auth::user()->balance,2), FILTER_SANITIZE_NUMBER_INT)) >= ($offer->price_offer != $listing->price ? $offer->price_offer : $listing->price) + $listing->delivery_price)
                    {{-- Balance button --}}
                    <a href="javascript:void(0);" data-toggle="modal" data-target="#PaymentModal" class="btn btn-lg btn-success m-r-5"><i class="far fa-money-bill" aria-hidden="true"></i> <span class="hidden-xs-down"> {{ trans('payment.balance') }} </span></a>
                @endif
                @if(config('settings.paypal'))
                    {{-- PayPal button --}}
                    <a href="{{url('offer/'.$offer->id.'/pay')}}" class="btn btn-lg btn-success m-r-5" id="pay-now-button"><i class="fab fa-paypal"></i> <span class="hidden-xs-down"> PayPal</span></a>
                @endif
                @if(config('settings.stripe'))
                    {{-- Stripe button --}}
                    <a href="javascript:void(0)" class="btn btn-lg btn-success" id="stripeCheckout"><i class="fab fa-cc-stripe"></i> <span class="hidden-xs-down"> Stripe</span></a>
                @endif
                </div>
            </div>
          @else
            {{-- Pending --}}
            <span class="btn btn-lg btn-warning block"><i class="fa fa-hourglass-end" aria-hidden="true"></i> {{ trans('payment.offer.pending') }}</span>
          @endif
        @endif
      </div>
      {{-- Payment status --}}
      <div class="payment-status {{ !$offer->payment ? 'hidden-xs-down' : '' }}">
        {{-- Status --}}
        <span class="text-light block">{{ trans('payment.offer.status') }}</span>
        @if($offer->payment)
          @if($offer->payment->status)
            {{-- Paid --}}
            <span class="status-value block">{{ trans('payment.offer.paid') }} <i class="fa fa-check-circle fa-lg text-success" aria-hidden="true"></i></span>
            <span class="block">{{ $offer->payment->created_at->diffForHumans() }}</span>
          @else
            {{-- Refunded --}}
            <span class="status-value block">{{ trans('payment.offer.refunded') }} <i class="fa fa-repeat fa-lg text-warning" aria-hidden="true"></i></span>
            <span class="block">{{ $offer->payment->updated_at->diffForHumans() }}</span>
          @endif
        @else
          {{-- Unpaid --}}
          <span class="status-value block">{{ trans('payment.offer.unpaid') }} <i class="fa fa-times-circle text-danger" aria-hidden="true"></i></span>
        @endif
      </div>
    </div>
  </div>

  {{-- Payment modal --}}
  @include('frontend.offer.inc.modal-payment')

@endif


@if(Auth::user()->can('edit_offers') && $offer->status > 0)
  {{-- Start user details for staff member --}}
  <div class="row">
    <div class="col-md-6">
      <section class="panel">
        <div class="panel-heading p-20">
          <a class="profile-link flex-center" href="{{$listing->user->url}}">
            {{-- User Avatar --}}
            <span class="avatar @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif m-r-10">
              <img src="{{$listing->user->avatar_square}}" alt="{{$listing->user->name}}'s Avatar"><i></i>
            </span>
            {{-- User Name & Location --}}
            <div>
              <span class="profile-name small">
                {{$listing->user->name}}
              </span>
              <span class="profile-location small">
              @if($listing->user->location)
                <img src="{{ asset('img/flags/' .   $listing->user->location->country_abbreviation . '.svg') }}" height="14"/> {{$listing->user->location->country_abbreviation}}, {{$listing->user->location->place}} <span class="postal-code">{{$listing->user->location->postal_code}}</span>
              @endif
              </span>
            </div>
          </a>
        </div>
        @if($offer->rating_id_offer)
          @php
          $rating_offer = \App\Models\User_Rating::find($offer->rating_id_offer);
          // Get rating color und icon
          switch ($rating_offer->rating) {
              case 0:
                  $rating_offer->icon = "fa-thumbs-down";
                  $rating_offer->bg = "bg-danger";
                  break;
              case 1:
                  $rating_offer->icon = "fa-minus";
                  $rating_offer->bg = "bg-dark";
                  break;
              case 2:
                  $rating_offer->icon = "fa-thumbs-up";
                  $rating_offer->bg = "bg-success";
                  break;
          }
          @endphp
          <div class="panel-body t-p">
            {{-- Start Rating --}}
            <section class="panel rating-panel {{ $rating_offer->bg }}">
              <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
              <div class="background-color" style="border-radius: 5px;"></div>

              {{-- Revoked overlay --}}
              @if(!$rating_offer->active && $offer->status == 2)
                <div class="declined flex-center">
                  <span class="declined-text"><i class="fa fa-repeat"></i> {{ trans('offers.general.revoked') }}</span>
                </div>
              @endif

              <div class="panel-body t-p">
                {{-- Rating icon --}}
                <i class="fa {{ $rating_offer->icon }} rating-icon" aria-hidden="true"></i>
                {{-- User avatar --}}
                <span class="avatar">
                  <img src="{{$offer->user->avatar_square}}" alt="{{$offer->user->name}}'s Avatar">
                </span>
                {{-- Notice --}}
                <div>
                  <span class="from-user">{{ trans('users.profile.rating_from', ['username' => $offer->user->name]) }}</span>
                  {{-- Rating notice --}}
                  @if($rating_offer->notice)
                    {{-- Head text with username from rater--}}
                    <span class="notice"><i class="fa fa-quote-left" aria-hidden="true"></i> {{$rating_offer->notice}} <i class="fa fa-quote-right" aria-hidden="true"></i></span>
                  @else
                    {{-- No notice --}}
                    <span class="notice">{{ trans('offers.status_complete.no_notice') }}</span>
                  @endif
                </div>

              </div>
            </section>
            {{-- End Rating --}}
          </div>
          {{-- Start staff tools for rating --}}
          @if($offer->status == 2)
            <div class="panel-footer p-10">
              <a class="btn btn-dark" href="{{ url('offer/admin/' . $offer->id . '/revoke/' . $rating_offer->id) }}">
                {{ $rating_offer->active ? 'Revoke' : 'Activate' }} <i class="icon fa {{ $rating_offer->icon }}" aria-hidden="true"></i> Rating from {{ $offer->user->name }}
              </a>
            </div>
          @endif
          {{-- End staff tools for rating --}}
        @endif
      </section>
    </div>
    <div class="col-md-6">
      <section class="panel">
        <div class="panel-heading p-20">
          <a class="profile-link flex-center" href="{{$offer->user->url}}">
            {{-- User Avatar --}}
            <span class="avatar @if($offer->user->isOnline()) avatar-online @else avatar-offline @endif m-r-10">
              <img src="{{$offer->user->avatar_square}}" alt="{{$offer->user->name}}'s Avatar"><i></i>
            </span>
            {{-- User Name & Location --}}
            <div>
              <span class="profile-name small">
                {{$offer->user->name}}
              </span>
              <span class="profile-location small">
              @if($offer->user->location)
                <img src="{{ asset('img/flags/' .   $offer->user->location->country_abbreviation . '.svg') }}" height="14"/> {{$offer->user->location->country_abbreviation}}, {{$offer->user->location->place}} <span class="postal-code">{{$offer->user->location->postal_code}}</span>
              @endif
              </span>
            </div>
          </a>
        </div>
        @if($offer->rating_id_listing)
          @php
          $rating_listing = \App\Models\User_Rating::find($offer->rating_id_listing);
          // Get rating color und icon
          switch ($rating_listing->rating) {
              case 0:
                  $rating_listing->icon = "fa-thumbs-down";
                  $rating_listing->bg = "bg-danger";
                  break;
              case 1:
                  $rating_listing->icon = "fa-minus";
                  $rating_listing->bg = "bg-dark";
                  break;
              case 2:
                  $rating_listing->icon = "fa-thumbs-up";
                  $rating_listing->bg = "bg-success";
                  break;
          }
          @endphp
          <div class="panel-body t-p">
            {{-- Start Rating --}}
            <section class="panel rating-panel {{ $rating_listing->bg }}">
              <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
              <div class="background-color" style="border-radius: 5px;"></div>
              {{-- Declined overlay --}}

              {{-- Revoked overlay --}}
              @if(!$rating_listing->active && $offer->status == 2)
                <div class="declined flex-center">
                  <span class="declined-text"><i class="fa fa-repeat"></i> {{ trans('offers.general.revoked') }}</span>
                </div>
              @endif

              <div class="panel-body t-p">
                {{-- Rating icon --}}
                <i class="fa {{ $rating_listing->icon }} rating-icon" aria-hidden="true"></i>
                {{-- User avatar --}}
                <span class="avatar">
                  <img src="{{$listing->user->avatar_square}}" alt="{{$listing->user->name}}'s Avatar">
                </span>
                {{-- Notice --}}
                <div>
                  <span class="from-user">{{ trans('users.profile.rating_from', ['username' => $listing->user->name]) }}</span>
                  {{-- Rating notice --}}
                  @if($rating_listing->notice)
                    {{-- Head text with username from rater--}}
                    <span class="notice"><i class="fa fa-quote-left" aria-hidden="true"></i> {{$rating_listing->notice}} <i class="fa fa-quote-right" aria-hidden="true"></i></span>
                  @else
                    {{-- No notice --}}
                    <span class="notice">{{ trans('offers.status_complete.no_notice') }}</span>
                  @endif
                </div>

              </div>
            </section>
            {{-- End Rating --}}
          </div>
          {{-- Start staff tools for rating --}}
          @if($offer->status == 2)
            <div class="panel-footer p-10">
              <a class="btn btn-dark" href="{{ url('offer/admin/' . $offer->id . '/revoke/' . $rating_listing->id) }}">
                {{ $rating_listing->active ? 'Revoke' : 'Activate' }} <i class="icon fa {{ $rating_listing->icon }}" aria-hidden="true"></i> Rating from {{ $listing->user->name }}
              </a>
            </div>
          @endif
          {{-- End staff tools for rating --}}
        @endif
      </section>
    </div>
  </div>
  {{-- End user details for staff member --}}
@endif


@if($offer->status == 0 || !Auth::user()->can('edit_offers'))
  {{-- Start User Profile Widget --}}
  <section class="panel">
    <div class="panel-body">
      <div class="flex-center-space">
        <a class="profile-link flex-center" href="{{$user->url}}">
          {{-- User Avatar --}}
          <span class="avatar @if($user->isOnline()) avatar-online @else avatar-offline @endif m-r-10">
            <img src="{{$user->avatar_square}}" alt="{{$user->name}}'s Avatar"><i></i>
          </span>
          {{-- User Name & Location --}}
          <div>
            <span class="profile-name small">
              {{$user->name}}
            </span>
            <span class="profile-location small">
            @if($user->location)
              <img src="{{ asset('img/flags/' .   $user->location->country_abbreviation . '.svg') }}" height="14"/> {{$user->location->country_abbreviation}}, {{$user->location->place}} <span class="postal-code">{{$user->location->postal_code}}</span>
            @endif
            </span>
          </div>
        </a>
        {{-- User Ratings --}}
        <div class="no-flex-shrink">
        @if(is_null($user->positive_percent_ratings))
          {{-- No Ratings --}}
          <span class="fa-stack fa-lg">
            <i class="fa fa-thumbs-up fa-stack-1x"></i>
            <i class="fa fa-ban fa-stack-2x text-danger"></i>
          </span>
          <span class="no-ratings small">{{ trans('users.general.no_ratings') }}</span>
        @else
          @php
            if($user->positive_percent_ratings > 70){
              $rating_icon = 'fa-thumbs-up text-success';
            }else if($user->positive_percent_ratings > 40){
              $rating_icon = 'fa-minus';
            }else{
              $rating_icon = 'fa-thumbs-down text-danger';
            }
          @endphp
          {{-- Ratings in percent --}}
          <span class="rating-percent small"><i class="fa {{$rating_icon}}" aria-hidden="true"></i> {{$user->positive_percent_ratings}}%</span>
          {{-- Ratings Count --}}
          <div class="rating-counts small">
            <span class="text-danger"><i class="fa fa-thumbs-down" aria-hidden="true"></i> {{$user->negative_ratings}}</span>&nbsp;&nbsp;
            <i class="fa fa-minus" aria-hidden="true"></i> {{$user->neutral_ratings}}&nbsp;&nbsp;
            <span class="text-success"><i class="fa fa-thumbs-up" aria-hidden="true"></i> {{$user->positive_ratings}}</span>
          </div>
        @endif
        </div>
      </div>
    </div>
    <div class="panel-footer text-light">
      {{-- Member since --}}
      <div class="p-20">
        {{-- Check if user is banned --}}
        @if($user->status)
          {{-- Member since --}}
          {{ trans('users.general.member_since', ['time' => $user->created_at->diffForHumans(null,true)]) }}
        @else
          {{-- User banned label --}}
          <span class="platform-label bg-danger m-t-10">{{ trans('users.profile.banned') }}</span>
        @endif
      </div>
      {{-- Links --}}
      <div>
        <a href="{{$user->url}}" class="button" id="save-submit">
          <i class="fa fa-user" aria-hidden="true"></i> {{ trans('users.general.profile') }}
        </a>
      </div>
    </div>
  </section>
  {{-- End User Profile Widget --}}
@endif

  {{-- Start user chat --}}
  <div class="panel offer-chat">
    {{-- Top shadow --}}
    <div class="shadow-top"></div>

    <div class="panel-body">
      <div class="chat-box">
        {{-- Load chat via ajax --}}
        <div class="chats messages" id="ajaxchat" style="height:400px; overflow:scroll;">
        </div>
      </div>
      {{-- Chat input --}}
      <div class="panel-footer">
      {!! Form::open(array('url'=>'offer/message', 'id'=>'form-messageadd', 'role'=>'form','files' => true , 'parsley-validate'=>'','novalidate'=>' ', 'class'=>'form-messageadd')) !!}
      <div class="input-group input-group-lg">
        {{-- Message input --}}
        <input type="text" class="form-control input input-lg" name="message" placeholder="{{ trans('offers.general.enter_message') }}">
        <input class="hidden" name="user_id" type="text" value="{{ encrypt(Auth::user()->id)  }}">
        <input class="hidden" name="thread_id" type="text" value="{{ encrypt($offer->thread_id) }}">
        {{-- Send button --}}
        <span class="input-group-btn">
          <button class="btn btn-primary send-message" type="button">
            <i class="fa fa-paper-plane" aria-hidden="true"></i> {{ trans('general.send') }}
          </button>
        </span>
      </div>
      {!! Form::close() !!}
      </div>
    </div>
  </div>
  {{-- End user chat --}}

{{-- Start offer button & modal --}}
@if($offer->status > 0 && !$offer->reported && (Auth::user()->id == $listing->user_id || Auth::user()->id == $offer->user_id))
  {{-- Report offer Button --}}
  <a href="#" data-toggle="modal" data-target="#modal_report_offer" aria-expanded="false" class="btn btn-lg btn-dark m-b-10">
    <i class="fa fa-life-ring" aria-hidden="true"></i> {{ trans('offers.general.report') }}
  </a>

  {{-- Start modal for report offer --}}
  <div class="modal fade modal-fade-in-scale-up modal-dark" id="modal_report_offer" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">

          <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

          <div class="title">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
            </button>
            {{-- Modal title (Decline offer) --}}
            <h4 class="modal-title" id="myModalLabel">
              <i class="fa fa-life-ring" aria-hidden="true"></i>
              {{ trans('offers.modal_report.title') }}
            </h4>
          </div>

        </div>
        {!! Form::open(array('url'=>'offer/report', 'id'=>'form-report', 'role'=>'form')) !!}
        <div class="modal-body">
          {{-- Info text --}}
          <div class="m-b-10"><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('offers.modal_report.info') }}</div>
          {{-- Reason input --}}
          {!! Form::textarea('reason', null,array('class'=>'form-control input','placeholder'=>trans('offers.modal_report.describe_problem'),'autocomplete'=>'off','rows'=>'4' )) !!}

        </div>

        <div class="modal-footer">
          {{-- Cancel button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
          </a>
          <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
          {{-- Submit button --}}
          &nbsp;<a class="btn btn-lg btn-danger btn-animate btn-animate-vertical" id="report-submit" href="javascript:void(0)">
            <span><i class="icon fa fa-life-ring" aria-hidden="true"></i> {{ trans('offers.modal_report.title') }}
            </span>
          </a>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
  {{-- End modal for report offer --}}
@endif
{{-- End offer button & modal --}}

@if($offer->status > 0 && $offer->reported)

  <section class="panel">
    <div class="panel-heading p-20 {{ $offer->report->status ? 'bg-success' : 'bg-danger'}}">
      <i class="fa {{ $offer->report->status ? 'fa-check' : 'fa-life-ring'}}" aria-hidden="true"></i> {!! trans('offers.general.reported_by', ['username' => $offer->report->user->name]) !!} {{ $offer->report->created_at->diffForHumans() }}
    </div>
    <div class="panel-body">
      {{ $offer->report->reason }}
    </div>
    @if($offer->report->status)
      <div class="panel-footer p-20 text-light">
        {{ trans('offers.general.report_closed', ['username' => $offer->report->staff->name]) }} {{ $offer->report->closed_at->diffForHumans() }}
      </div>
    @endif
  </section>
@endif

{{-- Start offer staff tools --}}
@if(Auth::user()->can('edit_offers'))
  <section class="panel">
    <div class="panel-body">
      @if(!$offer->declined && $offer->status < 2)
        {{-- Close offer & listing button --}}
        <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/close') }}">
          <i class="icon fa fa-tag" aria-hidden="true"></i> Close Offer & Listing
        </a>
        {{-- Close offer & reopen listing button --}}
        <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/close/reopen') }}">
          <i class="icon fa fa-tag" aria-hidden="true"></i> Close Offer & Reopen Listing
        </a>
      @else
        @if($listing->status == 2)
          {{-- Reopen listing button --}}
          <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/close/reopen') }}">
            <i class="icon fa fa-tag" aria-hidden="true"></i> Reopen Listing
          </a>
        @else
          {{-- Close listing button --}}
          <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/close/') }}">
            <i class="icon fa fa-tag" aria-hidden="true"></i> Close Listing
          </a>
        @endif
      @endif
      {{-- Ban seller --}}
      <span class="staff-tools-seperator"></span>
      @if($listing->user->id != Auth::user()->id)
        <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/ban/' . $listing->user->id) }}">
          <i class="icon fa fa-user-times" aria-hidden="true"></i> {{ $listing->user->status ? 'Ban' : 'Unban' }} {{ $listing->user->name }}
        </a>
      @endif
      {{-- Ban buyer --}}
      @if($offer->user->id != Auth::user()->id)
        <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/' . $offer->id . '/ban/' . $offer->user->id) }}">
          <i class="icon fa fa-user-times" aria-hidden="true"></i> {{ $offer->user->status ? 'Ban' : 'Unban' }} {{ $offer->user->name }}
        </a>
      @endif
      @if($offer->reported)
      <span class="staff-tools-seperator"></span>
      {{-- Close report --}}
      <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/admin/report/close/' . $offer->id) }}">
        <i class="icon fa fa-life-ring" aria-hidden="true"></i> {{ $offer->report->status ? 'Reopen' : 'Close'}} Report
      </a>
      @endif
      {{-- Payment options --}}
      @if($offer->payment && Auth::user()->can('edit_payments'))
      <span class="staff-tools-seperator"></span>
        {{-- Refund money button --}}
        @if($offer->payment->status && $offer->payment->transactions()->where('type','sale')->count() == 0)
          <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/' . $offer->id . '/pay/refund') }}" id="refund-money">
            <i class="icon fas fa-money-bill" aria-hidden="true"></i><i class="icon fa fa-undo" aria-hidden="true"></i> Refund money to {{$offer->payment->user->name}}
          </a>
        @endif
        {{-- Release money button --}}
        @if($offer->payment->transactions()->where('type','sale')->count() == 0 && $offer->payment->status)
          <a class="btn btn-dark m-b-5 m-t-5 m-r-5" href="{{ url('offer/' . $offer->id . '/pay/release') }}" id="release-money">
            <i class="icon fas fa-money-bill" aria-hidden="true"></i> Release money to {{$listing->user->name}}
          </a>
        @endif
      @endif
    </div>
  </section>
@endif
{{-- End offer staff tools --}}

{{-- Start Modals for accept and decline offers --}}
@if($offer->status == 0 && Auth::user()->id == $listing->user_id)
  {{-- Start modal for accept offer --}}
  <div class="modal fade modal-fade-in-scale-up modal-success" id="modal_accept" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">

          <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

          <div class="title">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
            </button>
            {{-- Title (Accept Offer) --}}
            <h4 class="modal-title" id="myModalLabel">
              <i class="fa fa-check" aria-hidden="true"></i>
              {{ trans('offers.modal_accept.title') }}
            </h4>
          </div>

        </div>

        <div class="modal-body">
          {{-- Info text --}}
          <span><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('offers.modal_accept.info') }}</span>

        </div>

        <div class="modal-footer">
          {!! Form::open(array('url'=>'offer/accept', 'id'=>'form-accept', 'role'=>'form')) !!}
          <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
          {!! Form::close() !!}
          {{-- Cancel button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
          </a>
          {{-- Accept button --}}
          <a class="btn btn-lg btn-success btn-animate btn-animate-vertical" id="accept-submit" href="javascript:void(0)">
            <span>
              <i class="icon fa fa-check" aria-hidden="true"></i> {{ trans('offers.modal_accept.title') }}
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
  {{-- End modal for accept offer --}}

  {{-- Start modal for decline offer --}}
  <div class="modal fade modal-fade-in-scale-up modal-danger" id="modal_decline" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header">

          <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

          <div class="title">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
            </button>
            {{-- Modal title (Decline offer) --}}
            <h4 class="modal-title" id="myModalLabel">
              <i class="fa fa-times" aria-hidden="true"></i>
              {{ trans('offers.modal_decline.title') }}
            </h4>
          </div>

        </div>
        {!! Form::open(array('url'=>'offer/decline', 'id'=>'form-decline', 'role'=>'form')) !!}
        <div class="modal-body">
          {{-- Info text --}}
          <div class="m-b-10"><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('offers.modal_decline.info') }}</div>
          {{-- Reason input --}}
          {!! Form::text('decline_note', null,array('class'=>'form-control input','placeholder'=>trans('offers.modal_decline.reason_placeholder'),'autocomplete'=>'off' )) !!}

        </div>

        <div class="modal-footer">
          {{-- Cancel button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical m-r-10">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
          </a>
          <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
          {{-- Submit button --}}
          <a class="btn btn-lg btn-danger btn-animate btn-animate-vertical" id="decline-submit" href="javascript:void(0)">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('offers.modal_decline.title') }}
            </span>
          </a>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
  {{-- End modal for decline offer --}}


@endif
{{-- End Modals for accept and decline offers --}}


@if($offer->status == 1)
  @if(!$offer->payment && $listing->payment && $offer->delivery && !isset($trade_game))
  @else
  {{-- start modal for close demand from offer user --}}
  <div class="modal fade modal-super-scaled modal-primary" id="modal_close_offer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

          <div class="modal-header">

            <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

            <div class="title">
              <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
              </button>
              <h4 class="modal-title" id="myModalLabel">
                <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                @if(Auth::user()->id == $offer->user_id)
                {{ trans('offers.modal_rating.title_offer', ['username' => $listing->user->name]) }}
                @else
                {{ trans('offers.modal_rating.title_listing', ['username' => $offer->user->name]) }}
                @endif
              </h4>
            </div>
          </div>

        <div class="modal-body">
          {!! Form::open(array('url'=>'offer/rating', 'id'=>'form-rating', 'role'=>'form')) !!}

          <div class="m-b-20" style="margin: 0 auto; text-align: center;">
            <div class="btn-group btn-group-lg" data-toggle="buttons">
              {{-- Negative button --}}
              <label class="btn btn-danger">
        		      <input type="radio" name="review" value="0"><i class="fa fa-thumbs-down font-size-30"></i> <br /> {{ trans('offers.modal_rating.negative') }}
        		  </label>
              {{-- Neutral button --}}
        		  <label class="btn btn-dark">
        		      <input type="radio" name="review" value="1"><i class="fa fa-minus font-size-30"></i> <br /> {{ trans('offers.modal_rating.neutral') }}
        		  </label>
              {{-- Positive button --}}
              <label class="btn btn-success active">
        		      <input type="radio" name="review" value="2" checked><i class="fa fa-thumbs-up font-size-30"></i> <br /> {{ trans('offers.modal_rating.positive') }}
        		  </label>
            </div>
          </div>

          <div class="form-group">
              {!! Form::text('review_note', null,array('class'=>'form-control input', 'placeholder'=>trans('offers.modal_rating.reason_placeholder'),'autocomplete'=>'off')) !!}
          </div>
          @if($offer->payment && $offer->payment->user_id == Auth::user()->id && $offer->payment->status && $offer->payment->transactions()->count() == 0 )
          {{-- Payment warning before rating --}}
          <div class="flex-center-space bg-danger b-r p-10">
            <div class="m-r-10"><i class="fa fa-info-circle font-size-30"></i></div>
            <div>{!! trans('payment.offer.rating_warning', ['username' => $listing->user->name]) !!}</div>
          </div>
          @endif
        </div>

        <div class="modal-footer">
          {{-- Cancel button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical">
            <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
          </a>
          <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
          {{-- Submit button --}}
          &nbsp;<a class="btn btn-lg btn-primary btn-animate btn-animate-vertical" href="javascript:void(0)" id="rate-submit">
              <span><i class="icon fa fa-thumbs-up" aria-hidden="true" id="rate-submit-icon"></i>
              {{ trans('offers.modal_rating.rate_button', ['username' =>Auth::user()->id == $offer->user_id ? $listing->user->name : $offer->user->name ]) }}
              </span>
          </a>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </div>
  {{-- end modal for close demand from offer user --}}
  @endif
@endif

@section('after-scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

@if((Auth::user()->id == $offer->user_id) && ($offer->delivery && $offer->status == '1' && $listing->payment) && (!isset($offer->payment)))
<script src="https://checkout.stripe.com/checkout.js"></script>
<script>
var handler = StripeCheckout.configure({
  key: '{{ config('settings.stripe_client_id') }}',
  image: '{{ $listing->game->image_square }}',
  email: '{{ Auth::user()->email }}',
  locale: 'auto',
  token: function(token) {
    window.location.replace('{{ url('offer/' . $offer->id . '/pay/stripe/success' ) }}/' + token.id);

  }
});


document.getElementById('stripeCheckout').addEventListener('click', function(e) {
  e.preventDefault();
  $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
  $(this).addClass('loading');
  // Open Checkout with further options:
  handler.open({
    name: '{{ config('settings.page_name') }}',
    description: '{{ $listing->game->name }}',
    zipCode: true,
    currency: '{{ config('settings.currency') }}',
    amount: {{ ($offer->price_offer ? $offer->price_offer : $listing->price) + ($listing->delivery_price ? $listing->delivery_price : '0')}},
    opened: function() {
      $('#stripeCheckout').html('<i class="fab fa-cc-stripe"></i> Stripe');
      $('#stripeCheckout').removeClass('loading');
    }
  });
});



// Close Checkout on page navigation:
window.addEventListener('popstate', function() {
  handler.close();
});
</script>
@endif

<script type="text/javascript">
$(document).ready(function(){

  @if(Auth::user()->can('edit_payments') && $offer->payment)
  $("#refund-money").click( function(e) {
      $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $(this).addClass('loading');
      if (!confirm("Do you really want to refund the money? You can't undo this action.")) {
        e.preventDefault();
        $(this).html('<i class="icon fa fa-money" aria-hidden="true"></i> Refund money to {{$offer->payment->user->name}}');
        $(this).removeClass('loading');
      }
  });

  $("#release-money").click( function(e) {
      $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $(this).addClass('loading');
      if (!confirm("Do you really want to release the money? You can't undo this action and a refund is not possible anymore.")) {
        e.preventDefault();
        $(this).html('<i class="icon fa fa-money" aria-hidden="true"></i> Release money to {{$listing->user->name}}');
        $(this).removeClass('loading');
      }
  });
  @endif

  @if($listing->payment && config('settings.payment'))
  {{-- Pay now button --}}
  $("#pay-now-button").click( function(){
    $(this).addClass('loading');
    $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    window.location = $(this).attr("href");
  });

  {{-- Payment submit --}}
  $("#payment-submit").click( function(){
    $('#payment-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#payment-submit').addClass('loading');
    $('#form-payment').submit();
  });
  @endif

  {{-- Report submit --}}
  $("#report-submit").click( function(){
    $('#report-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#report-submit').addClass('loading');
    $('#form-report').submit();
  });

  {{-- Accept submit --}}
  $("#accept-submit").click( function(){
    $('#accept-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#accept-submit').addClass('loading');
    $('#form-accept').submit();
  });

  {{-- Accept submit --}}
  $("#decline-submit").click( function(){
    $('#decline-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#decline-submit').addClass('loading');
    $('#form-decline').submit();
  });

  {{-- Rate submit --}}
  $("#rate-submit").click( function(){
    $('#rate-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#rate-submit').addClass('loading');
    $('#form-rating').submit();
  });

  var num = null;
  $(".btn-group > .btn").on("click", function(){
    num = $(this).find('input[name="review"]').val();
    switch (num) {
      case "0":
        $("#rate-submit-icon").removeClass().addClass('icon fa fa-thumbs-down');
        break;
      case "1":
        $("#rate-submit-icon").removeClass().addClass('icon fa fa-minus');
        break;
      case "2":
        $("#rate-submit-icon").removeClass().addClass('icon fa fa-thumbs-up');
        break;
    }
  });

  {{-- Load Ajax Chat --}}
  $("#ajaxchat").load('{{ url('ajaxchat/' . $offer->id ) }}', function() {
      $('#ajaxchat').animate({scrollTop: $('#ajaxchat').get(0).scrollHeight}, 1000);
  });

  {{-- Refresh chat every 10 seconds --}}
  var refreshId = setInterval(function() {
    $("#ajaxchat").load('{{ url('ajaxchat/' . $offer->id ) }}');
  }, 10000);

  {{-- Refresh chat button --}}
  $("#refresh_chat").click(function(e){
      $("#ajaxchat").load('{{ url('ajaxchat/' . $offer->id ) }}', function() {
          $('#ajaxchat').animate({scrollTop: $('#ajaxchat').get(0).scrollHeight}, 1000);
      });
      e.preventDefault();
  })

  {{-- Send message on enter press --}}
  $('input[name=message]').keypress(function (e) {
    if (e.which == 13) {
      $('.send-message').click();
      return false;
    }
  });

  {{-- Send message --}}
  $(".send-message").click(function(){
    if(!($('input[name=message]').val().length === 0)) {
      $.ajax({
        beforeSend: function () {
          $(".send-message").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
        },
        url: $("#form-messageadd").attr("action"),
        type: 'POST',
        data: $("#form-messageadd").serialize(),
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        success: function(data){
          $(".send-message").html('<i class="fa fa-paper-plane" aria-hidden="true"></i> {{ trans('general.send') }}');
          $("#ajaxchat").load('{{ url('ajaxchat/' . $offer->id ) }}', function() {
            $('#ajaxchat').animate({scrollTop: $('#ajaxchat').get(0).scrollHeight}, 500);
          });
        }
      })
      $('input[name=message]').val('');
    }
  });

});
</script>
@endsection

@stop
