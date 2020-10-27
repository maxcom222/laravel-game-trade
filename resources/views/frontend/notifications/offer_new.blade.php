@php
$listing = $listings->where('id', $notification->data['listing_id'] )->first();
$offer = $offers->where('id', $notification->data['offer_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{$offer->url}}" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink m-r-10">
      <img src="{{$listing->game->image_square_tiny}}">
    </span>
    {{-- Icon between --}}
    <i class="fa {{ $notification->data['trade'] ? 'fa-exchange' : 'fa-shopping-basket'}}  no-flex-shrink" aria-hidden="true"></i>
    {{-- Trade game or price --}}
    @if($notification->data['trade'])
      <span class="avatar no-flex-shrink m-l-10">
          @if($offer->game)
            <img src="{{$offer->game->image_square_tiny}}">
          @endif
      </span>
    @else
      <span class="text-success f-w-700 no-flex-shrink m-l-10">{{$offer->price_offer_formatted}}</span>
    @endif
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      @if($notification->data['trade'])
        {{ trans('notifications.offer_new_trade', ['username' => $offer->user->name, 'gamename' => $listing->game->name, 'tradegame' => $offer->game ? $offer->game->name : 'removed']) }}
      @else
        {{ trans('notifications.offer_new_buy', ['username' => $offer->user->name, 'gamename' => $listing->game->name, 'price' => $offer->price_offer_formatted]) }}
      @endif
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa {{ $notification->data['trade'] ? 'fa-exchange' : 'fa-shopping-basket' }}"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
