@php
$user = $users->where('id', $notification->data['user_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{ url('messages') }}" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    <div class="circle-icon bg-primary">
      <i class="fa fa-envelope"></i>
    </div>
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink m-l-10">
      <img src="{{$user->avatar_square_tiny}}">
    </span>
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      {{ trans('notifications.message_new', ['username' => $user->name]) }}
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa fa-envelope"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
