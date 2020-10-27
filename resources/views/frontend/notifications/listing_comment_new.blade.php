@php
$listing = $listings->where('id', $notification->data['listing_id'] )->first();
$user = $users->where('id', $notification->data['user_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{$listing->url_slug}}#!comments" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink">
      <img src="{{$listing->game->image_square_tiny}}">
    </span>
    {{-- User avatar --}}
    <span class="avatar no-flex-shrink m-l-10">
      <img src="{{$user->avatar_square_tiny}}">
    </span>
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      {{ trans('notifications.comment_new', ['username' => $user->name, 'gamename' => $listing->game->name]) }}
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa fa-comment"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
