@php
$listing = $listings->where('id', $notification->data['listing_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{$listing->url_slug}}" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    <div class="circle-icon bg-danger">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink m-l-10">
      <img src="{{$listing->game->image_square_tiny}}">
    </span>
    {{-- Icon between --}}
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      {{ trans('notifications.push.price_alert_message', ['game_name' => $listing->game->name, 'platform_name' => $listing->game->platform->name, 'price' => $listing->price_formatted]) }}
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fas fa-exclamation-triangle"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
