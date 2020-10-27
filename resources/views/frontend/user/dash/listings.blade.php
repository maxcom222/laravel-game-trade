@extends(Theme::getLayout())

@section('subheader')
  <div class="subheader {{ $user->listings->count() == 0 && $listings_trashed_count == 0  ? '' : 'tabs' }}">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    {{-- Subheader title (Listings) --}}
    <div class="content">
      <span class="title"><i class="fa fa-tags"></i> {{ trans('general.listings') }}</span>
    </div>

    <div class="tabs">
      {{-- Active tab --}}
      @if((count($user->listings->where('status',0))+count($user->listings->where('status',1))) != 0)
      <a class="tab {{  Request::is('dash/listings') ? 'active' : ''}}" href="{{url('dash/listings')}}">
        {{ trans('users.dash.active') }} <span class="tag tag-pill tag-dash">{{count($user->listings->where('status',0))+count($user->listings->where('status',1))}}</span>
      </a>
      @endif
      {{-- Complete tab --}}
      @if(count($user->listings->where('status',2)) != 0)
      <a class="tab {{  Request::is('dash/listings/complete') ? 'active' : ''}}" href="{{url('dash/listings/complete')}}">
        {{ trans('users.dash.complete') }} <span class="tag tag-pill tag-dash">{{count($user->listings->where('status',2))}}</span>
      </a>
      @endif
      {{-- Deleted tab --}}
      @if($listings_trashed_count != 0)
      <a class="tab {{  Request::is('dash/listings/deleted') ? 'active' : ''}}"  href="{{url('dash/listings/deleted')}}">
        <i class="fa fa-trash m-r-5" aria-hidden="true"></i> <span class="tag tag-pill tag-dash">{{$listings_trashed_count}}</span>
      </a>
      @endif

    </div>

  </div>

@stop


@section('content')
{{-- Pagination Link before listings --}}
{{ $listings->links() }}

@forelse($listings as $listing)
  {{-- Start Listing --}}
  <section class="panel @if(!is_null($listing->deleted_at)) grayscale @endif">

    {{-- Start Listing Header --}}
    <div class="panel-heading listing-heading">
      <div class="flex-center-space">
        <div class="flex-center">
          {{-- Game Cover --}}
          <div class="m-r-10">
            <span class="avatar">
              <img src="{{ $listing->game->image_square_tiny }}" alt="{{ $listing->game->name }}">
            </span>
          </div>
          {{-- Game Name + platform --}}
          <div>
            <div class="title">{{ $listing->game->name }}</div>
            <span class="platform-label" style="background-color:{{ $listing->game->platform->color }};"> {{ $listing->game->platform->name }} </span>
          </div>
        </div>
        <div class="flex-center no-flex-shrink">
        @if($listing->sell)
          <div class="sell-status">
            <span>{{$listing->getPrice()}}</span>
            {{-- Check if user accept price suggestions --}}
            @if($listing->sell_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @endif
          </div>
        @endif
        @if($listing->trade)
          <div class="trade-status m-l-5">
            <span><i class="fa fa-exchange"></i></span>
            {{-- Check if user accept trade suggestions --}}
            @if($listing->trade_negotiate)
              <span class="suggestion"><i class="fa fa-retweet" aria-hidden="true"></i></span>
            @endif
          </div>
        @endif
        </div>
      </div>
    </div>
    {{-- End Listing Header --}}

    <div class="listing-body">

      @forelse($listing->offers as $offer)
      <div class="listing {{ !is_null($offer->thread) && $offer->thread->isUnread(auth()->user()->id) ? 'notify' : '' }}" style="position: relative;">
        {{-- Declined overlay --}}
        @if($offer->declined == 1)
        <div class="declined flex-center">
          <a class="declined-text" href="{{ $offer->url }}"><i class="fa fa-times"></i> {{ trans('users.dash.declined') }}</a>
        </div>
        @endif

        {{-- Offer price --}}
        @if(!is_null($offer->price_offer))
        <div class="sell-details flex-center">
          <div>{{ $offer->price_offer_formatted }}</div>
          {{-- Price suggestion percentage down --}}
          @if($listing->price != 0 && $offer->price_offer < $listing->price)
          @php $perc = abs(round(($offer->price_offer / $listing->price) * 100 - 100)); @endphp
          <div class="price-suggestion down flex-center">
            <span class="m-t-10">{{ strlen($perc) <= 3 ? $perc : '--' }}% <i class="fa fa-caret-down" aria-hidden="true"></i></span>
          </div>
          @endif
          {{-- Price suggestion percentage up --}}
          @if($listing->price != 0 && $offer->price_offer > $listing->price)
          @php $perc = round(($offer->price_offer / $listing->price) * 100 - 100); @endphp
          <div class="price-suggestion up flex-center">
            <span><i class="fa fa-caret-up" aria-hidden="true"></i> {{ strlen($perc) <= 3 ? $perc : '++'  }}% </span>
          </div>
          @endif
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

              {{-- Additional charge from user --}}
              @if(!is_null($offer->additional_type) && $offer->additional_type == 'give')
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

              {{-- Trade game --}}
              @if(isset($offer->game))
              <div class="trade-offer-game flex-center">
                {{-- Game cover --}}
                <div class="avatar m-r-10">
                  <img src="{{ $offer->game->image_square_tiny }}" alt="{{ $offer->game->name }}">
                </div>
                <div>
                  {{-- Game title & platform / icon when suggestion --}}
                  <div class="offer-game-title">@if(!$offer->trade_from_list)<span class="m-r-5"><i class="fa fa-retweet" aria-hidden="true"></i></span>@endif{{ $offer->game->name }}</div>
                  <span class="platform-label" style="background-color:{{ $offer->game->platform->color }};">{{ $offer->game->platform->name }} </span>
                </div>
              </div>
              @endif

              {{-- Additional charge from partner --}}
              @if(!is_null($offer->additional_type) && $offer->additional_type == 'want')
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
              <a href="{{$offer->user->url}}" class="offer-user flex-center">
                {{-- Avatar --}}
                <div class="avatar @if($offer->user->isOnline()) avatar-online @else avatar-offline @endif m-r-10">
                  <img src="{{ $offer->user->avatar_square_tiny }}" alt="{{ $offer->user->name }}'s Avatar'"><i></i>
                </div>
                <div>
                  {{-- Username --}}
                  <span class="offer-username">{{ $offer->user->name }}</span>
                  @if($offer->user->location)
                  {{-- User location --}}
                  <img src="{{ asset('img/flags/' .   $offer->user->location->country_abbreviation . '.svg') }}" height="14"/> {{$offer->user->location->country_abbreviation}}, {{$offer->user->location->place}} <span class="postal-code">{{$offer->user->location->postal_code}}</span>
                  @endif
                </div>
              </a>
              {{-- End offer user --}}

            </div>

          </div>
        </div>

        {{-- Offer waiting status --}}
        @if($offer->status == 0 && $offer->declined == 0)
        <a href="{{ $offer->url }}">
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
            <i class="fa fa-hourglass-end" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('payment.offer.awaiting_payment') }}</span>
          </div>
          </a>
        @else
          @if($offer->status == 1 && is_null($offer->rating_id_listing) )
          <a href="{{ $offer->url }}">
          <div class="details-button status-1">
            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('users.dash.listings.status_1',['username' => $offer->user->name]) }}</span>
          </div>
          </a>
          @elseif($offer->status == 1 && is_null($offer->rating_id_offer))
          <a href="{{ $offer->url }}">
          <div class="details-button status-1">
            <i class="fa fa-hourglass" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('users.dash.listings.status_1_wait') }}</span>
          </div>
          </a>
          @endif
        @endif

        {{-- Finished offer status --}}
        @if($offer->status == 2 && $offer->rating_id_offer)
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
        <a href="{{ $offer->url }}">
        {{-- Details button with rating --}}
        <div class="details-button status-2 {{$rating->class}}"><i class="fa {{$rating->icon}}" aria-hidden="true"></i>
          <span class="hidden-sm-down"> {{ trans('general.details') }}</span>
        </div>
        </a>
        @endif


      </div>

      @empty
      <div class="listing-no-offers">
        <i class="far fa-frown" aria-hidden="true"></i> {{ trans('users.dash.listings.no_offers') }}
      </div>
      @endforelse

    </div>

    {{-- Start Listing Footer --}}

    <div class="panel-footer @if(!is_null($listing->deleted_at))padding @endif">
      {{-- Listing created at --}}
      <div class="listing-footer-time @if(!is_null($listing->deleted_at))deleted @endif">
        {{$listing->created_at->diffForHumans()}} <br/>
        <i class="fa fa-chart-bar" aria-hidden="true"></i> {{$listing->clicks}} {{ trans('users.dash.listings.clicks') }}
      </div>
      {{-- Footer Buttons --}}
      <div>
      @if(is_null($listing->deleted_at))
        @if($listing->status == 0 || is_null($listing->status))
        <a href="javascript:void(0)" data-toggle="modal" data-target="#modal_delete_{{$listing->id}}" class="button additional">
          <i class="fa fa-trash" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.delete') }}</span>
        </a><a href="{{ $listing->url_slug . '/edit' }}" class="button additional">
          <i class="fa fa-edit" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.edit') }}</span>
        </a>@endif<a href="{{ $listing->url_slug }}" class="button">
          <i class="fa fa-caret-square-right" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.details') }}</span>
        </a>
      @endif
      </div>

    </div>
    {{-- End Listing Footer --}}
  </section>
  {{-- End Listing --}}

  {{-- Start modal for delete listing --}}
  @if($listing->status == 0 || is_null($listing->status))
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
              <i class="fa fa-trash" aria-hidden="true"></i>
              {{ trans('users.modal_delete_listing.title', ['gamename' => $listing->game->name]) }}
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

@empty
  {{-- Start empty list message --}}
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
  {{-- End empty list message --}}
@endforelse

{{-- Pagination Link after listings --}}
{{ $listings->links() }}

@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- Delete submit --}}
  $("#delete-submit").click( function(){
    $('#delete-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#delete-submit').addClass('loading');
    $('#form-delete').submit();
  });
});
</script>

@endsection

@stop
