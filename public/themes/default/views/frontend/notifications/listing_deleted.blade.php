@php
$listing = $listings->where('id', $notification->data['listing_id'] )->first();
$offer = $offers->where('id', $notification->data['offer_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{$offer->url}}" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    <div class="circle-icon bg-danger">
      <i class="fa fa-trash"></i>
    </div>
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink m-l-10">
      <img src="{{$listing->game->image_square_tiny}}">
    </span>
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      {{ trans('notifications.listing_deleted', ['username' => $listing->user->name, 'gamename' => $listing->game->name]) }}
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa fa-trash"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
