@php
$listing = $listings->where('id', $notification->data['listing_id'] )->first();
$offer = $offers->where('id', $notification->data['offer_id'] )->first();
@endphp
{{-- Start Notification --}}
<a class="notification hvr-grow-shadow2 {{ $notification->read_at ? 'grayscale' : '' }}" href="{{$offer->url}}" data-notif-id="{{$notification->id}}">
  <div class="icons">
    {{-- Notification icon --}}
    @if($notification->data['status'] == 'declined')
    <div class="circle-icon bg-danger">
      <i class="fa fa-times"></i>
    </div>
    @else
    <div class="circle-icon bg-success">
      <i class="fa fa-check"></i>
    </div>
    @endif
    {{-- Listing Game --}}
    <span class="avatar no-flex-shrink m-l-10">
      <img src="{{$listing->game->image_square_tiny}}">
    </span>
  </div>
  <div class="info">
    {{-- Notification text --}}
    <h1>
      @if($notification->data['status'] == 'declined')
        {{ trans('notifications.offer_status_declined', ['username' => $listing->user->name, 'gamename' => $listing->game->name]) }}
      @else
        {{ trans('notifications.offer_status_accepted', ['username' => $listing->user->name, 'gamename' => $listing->game->name]) }}
      @endif
    </h1>
    {{-- Notificaion icon and date --}}
    <p><i class="fa {{ $notification->data['status'] == 'declined' ? 'fa-times' : 'fa-check' }}"></i> {{$notification->created_at->diffForHumans()}}</p>
  </div>
</a>
{{-- End notification --}}
