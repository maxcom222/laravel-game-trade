@extends(Theme::getLayout())

@section('subheader')

  <div class="subheader {{ count($user->offers) == 0 && $offers_trashed_count == 0 ? '' : 'tabs' }}">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    <div class="content">
      <span class="title"><i class="fa fa-briefcase"></i> {{ trans('general.offers') }}</span>
    </div>

    <div class="tabs">
      {{-- Active tab --}}
      @if((count($user->offers->where('status',0)->where('declined',0)) + count($user->offers->where('status',1)->where('declined',0))) != 0)
      <a class="tab {{  Request::is('dash/offers') ? 'active' : ''}}" href="{{url('dash/offers')}}">
        {{ trans('users.dash.active') }} <span class="tag tag-pill tag-dash">{{count($user->offers->where('status',0)->where('declined',0)) + count($user->offers->where('status',1)->where('declined',0))}}</span>
      </a>
      @endif
      {{-- Complete tab --}}
      @if(count($user->offers->where('status',2)) != 0)
      <a class="tab {{  Request::is('dash/offers/complete') ? 'active' : ''}}" href="{{url('dash/offers/complete')}}">
        {{ trans('users.dash.complete') }} <span class="tag tag-pill tag-dash">{{count($user->offers->where('status',2))}}</span>
      </a>
      @endif
      {{-- Declined tab --}}
      @if(count($user->offers->where('declined',1)) != 0)
      <a class="tab {{  Request::is('dash/offers/declined') ? 'active' : ''}}" href="{{url('dash/offers/declined')}}">
        {{ trans('users.dash.declined') }} <span class="tag tag-pill tag-dash">{{count($user->offers->where('declined',1))}}</span>
      </a>
      @endif
      {{-- Deleted tab --}}
      @if($offers_trashed_count != 0)
      <a class="tab {{  Request::is('dash/offers/deleted') ? 'active' : ''}}" href="{{url('dash/offers/deleted')}}">
        <i class="fa fa-trash m-r-5" aria-hidden="true"></i> <span class="tag tag-pill tag-dash">{{$offers_trashed_count}}</span>
      </a>
      @endif

    </div>

  </div>

@stop


@section('content')

{{ $offers->links() }}

@forelse($offers as $offer)

    {{-- Start Listing --}}
    <section class="panel @if(!is_null($offer->deleted_at)) grayscale @endif {{ !is_null($offer->thread) && $offer->thread->isUnread(auth()->user()->id) ? 'notify' : '' }}">
      {{-- Start Listing Header --}}
      <div class="panel-heading listing-heading">
        <div class="flex-center-space">
          <div class="flex-center">
            {{-- Game Cover --}}
            <div class="m-r-10">
              <span class="avatar">
                <img src="{{ $offer->listing->game->image_square_tiny }}" alt="{{ $offer->listing->game->name }}">
              </span>
            </div>
            {{-- Game Name + platform --}}
            <div>
              <div class="title">{{ $offer->listing->game->name  }}</div>
              <span class="platform-label" style="background-color:{{ $offer->listing->game->platform->color }};"> {{ $offer->listing->game->platform->name }} </span>
            </div>
          </div>
        </div>
      </div>
      {{-- End Listing Header --}}


    <div class="listing-body">

      <div class="listing">

        {{-- Offer price --}}
        @if(!is_null($offer->price_offer))
        <div class="sell-details">
          {{ $offer->price_offer_formatted }}
        </div>
        {{-- Offer trade game --}}
        @elseif(isset($offer->game))
        <div class="trade-details">
          <i class="fa fa-exchange"></i>
        </div>
        @endif

        <div class="listing-detail-wrapper">
          <div class="listing-detail">
            <div class="listing-detail-fix flex-center">

              {{-- Additional charge from partner --}}
              @if(!is_null($offer->additional_type) && $offer->additional_type == 'give')
              <div class="trade-offer-game flex-center">
                <div class="additional-charge flex-center">
                  <div class="charge-icon">
                    <i class="fa fa-plus"></i>
                  </div>
                  <div class="charge-money">
                    {{ money($offer->additional_charge, Config::get('settings.currency')) }}
                  </div>
                </div>
              </div>
              @endif

              {{-- Trade game --}}
              @if(isset($offer->game))
              <div class="trade-offer-game flex-center">
                {{-- Game cover --}}
                <div class="avatar m-r-10">
                  <img src="{{ $offer->game->image_square_tiny }}" alt="{{ $offer->game->name }}">
                </div>
                <div>
                  {{-- Game title & platform --}}
                  <div class="offer-game-title">{{ $offer->game->name }}</div>
                  <span class="platform-label" style="background-color:{{ $offer->game->platform->color }};">{{ $offer->game->platform->name }} </span>
                </div>
              </div>
              @endif

              {{-- Additional charge from user --}}
              @if(!is_null($offer->additional_type) && $offer->additional_type == 'want')
              <div class="trade-offer-game flex-center">
                <div class="additional-charge flex-center">
                  <div class="charge-money partner">
                    {{ money($offer->additional_charge, Config::get('settings.currency')) }}
                  </div>
                  <div class="charge-icon partner">
                    <i class="fa fa-minus"></i>
                  </div>
                </div>
              </div>
              @endif

              {{-- Delivery or pickup --}}
              @if(!is_null($offer->price_offer))
              <div class="delivery-pickup flex-center">
                @if($offer->delivery)
                  <i class="fa fa-truck" aria-hidden="true"></i>
                @else
                  <i class="fa fa-handshake" aria-hidden="true"></i>
                @endif
              </div>
              @endif

              {{-- Start offer user --}}
              <a href="{{$offer->listing->user->url}}" class="offer-user flex-center">
                {{-- Avatar --}}
                <div class="avatar @if($offer->listing->user->isOnline()) avatar-online @else avatar-offline @endif m-r-10">
                  <img src="{{ $offer->listing->user->avatar_square_tiny }}" alt="{{ $offer->listing->user->name }}'s Avatar'"><i></i>
                </div>
                <div>
                  {{-- Username --}}
                  <span class="offer-username">{{ $offer->listing->user->name }}</span>
                  @if($offer->listing->user->location)
                  {{-- User location --}}
                  <img src="{{ asset('img/flags/' .   $offer->listing->user->location->country_abbreviation . '.svg') }}" height="14"/> {{$offer->listing->user->location->country_abbreviation}}, {{$offer->listing->user->location->place}} <span class="postal-code">{{$offer->listing->user->location->postal_code}}</span>
                  @endif
                </div>
              </a>
              {{-- End offer user --}}

            </div>

          </div>
        </div>

        {{-- Offer waiting status --}}
        @if($offer->status == 0 && $offer->declined == 0)
        <a href="{{ url('offer/' . $offer->id)}}">
        <div class="details-button status-0">
          <i class="fa fa-hourglass" aria-hidden="true"></i></i>
          <span class="hidden-sm-down"> {{ trans('users.dash.listings.status_0') }}</span>
        </div>
        </a>
        @endif

        {{-- Offer declined status --}}
        @if($offer->status == 0 && $offer->declined == 1)
        <a href="{{ $offer->url }}">
        <div class="details-button bg-danger">
          <i class="fa fa-times" aria-hidden="true"></i></i>
        </div>
        </a>
        @endif

        {{-- Rate status / Pay status --}}
        @if($offer->status == 1 && $offer->listing->payment && $offer->delivery && !$offer->payment && !$offer->trade_game)
          <a href="{{ $offer->url }}">
          <div class="details-button status-1">
            <i class="fa fa-times-circle" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('payment.offer.unpaid') }}</span>
          </div>
          </a>
        @else
          @if($offer->status == 1 && is_null($offer->rating_id_offer) )
          <a href="{{ url('offer/' . $offer->id)}}">
          <div class="details-button status-1">
            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('users.dash.listings.status_1',['username' => $offer->listing->user->name]) }}</span>
          </div>
          </a>
          @elseif($offer->status == 1 && is_null($offer->rating_id_listing))
          <a href="{{ url('offer/' . $offer->id)}}">
          <div class="details-button status-1">
            <i class="fa fa-hourglass" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('users.dash.listings.status_1_wait') }}</span>
          </div>
          </a>
          @endif
        @endif

        {{-- Finished offer status --}}
        @if($offer->status == 2 && $offer->rating_id_listing)
        @php

        $rating = \App\Models\User_Rating::find($offer->rating_id_offer);

        switch ($rating->rating) {
            case 0:
                $rating->icon = 'fa-thumbs-down';
                $rating->class = 'bad';
                break;
            case 1:
                $rating->icon = 'fa-minus';
                $rating->class = 'avg';
                break;
            case 2:
                $rating->icon = 'fa-thumbs-up';
                $rating->class = 'good';
                break;
        }

        @endphp
        <a href="{{ url('offer/' . $offer->id)}}">
        {{-- Details button with rating --}}
        <div class="details-button status-2 {{$rating->class}}"><i class="fa {{$rating->icon}}" aria-hidden="true"></i>
          <span class="hidden-sm-down"> {{ trans('general.details') }}</span>
        </div>
        </a>
        @endif


      </div>

    </div>

    <div class="panel-footer @if(!is_null($offer->deleted_at))padding @endif">
      <div class="listing-footer-time @if(!is_null($offer->deleted_at))deleted @endif">
        {{$offer->created_at->diffForHumans()}}
      </div>
      <div>
      @if(is_null($offer->deleted_at))
        @if($offer->status == 0 || is_null($offer->status))
        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal_delete_{{$offer->id}}" class="button additional">
          <i class="fa fa-trash" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.delete') }}</span>
        </a>@endif<a href="{{ url('offer/' . $offer->id)}}" class="button">
          <i class="fa fa-caret-square-right" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.details') }}</span>
        </a>
      @endif
      </div>
    </div>

  </section>


  {{-- Start modal for delete offer --}}
  @if($offer->status == 0 || is_null($offer->status))
  <div class="modal fade modal-fade-in-scale-up modal-danger" id="modal_delete_{{$offer->id}}" tabindex="-1" role="dialog">
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
              <i class="fa fa-trash" aria-hidden="true"></i>
              {{ trans('users.modal_delete_offer.title', ['gamename' => $offer->listing->game->name]) }}
            </h4>
          </div>

        </div>

        <div class="modal-body">
          {{-- Delete info --}}
          <span><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('users.modal_delete_offer.info') }}</span>

        </div>

        <div class="modal-footer">
          {!! Form::open(array('url'=>'offer/delete', 'id'=>'form-delete', 'role'=>'form')) !!}
          {{-- Close button --}}
          <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical m-r-10"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span></a>
          <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
          {{-- Delete button --}}
          <button class="btn btn-lg btn-danger btn-animate btn-animate-vertical" type="submit" id="delete-submit">
            <span><i class="icon fa fa-trash" aria-hidden="true"></i> {{ trans('users.modal_delete_offer.delete_listing') }}
            </span>
          </button>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
  @endif
  {{-- End modal for delete offer --}}


@empty
  {{-- Start empty list message --}}
  <div class="empty-list">
    {{-- Icon --}}
    <div class="icon">
      <i class="far fa-frown" aria-hidden="true"></i>
    </div>
    {{-- Text --}}
    <div class="text">
      {{ trans('offers.general.no_offers') }}
    </div>
  </div>
  {{-- End empty list message --}}
@endforelse

{{ $offers->links() }}

@stop
