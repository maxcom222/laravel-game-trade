@extends(Theme::getLayout())


@section('subheader')
  {{-- Start subheader --}}
  <div class="subheader {{ $user->unreadNotifications()->count() > 0 ? 'tabs' : '' }}">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>
    {{-- Subheader title (Notifications) --}}
    <div class="content">
      <span class="title"><i class="fa fa-bell"></i> {{ trans('notifications.title') }}</span>
    </div>

    @if($user->unreadNotifications()->count() > 0)
      <div class="tabs">
        {{-- Mark all as  read --}}
        <a class="tab" href="{{url('dash/notifications/read/all')}}">
          <i class="fa fa-check"></i> {{ trans('notifications.mark_all_read') }}
        </a>
      </div>
    @endif

  </div>
  {{-- End subheader --}}
@stop


@section('content')
  {{-- Load data for notifications --}}
  @php
  $listings = \App\Models\Listing::whereIn('id', array_column($user->notifications->pluck('data')->toArray(),'listing_id'))->withTrashed()->get();
  $listings->load('game','game.platform','game.giantbomb');
  $offers = \App\Models\Offer::whereIn('id', array_column($user->notifications->pluck('data')->toArray(),'offer_id'))->withTrashed()->get();
  $offers->load('game','user');
  $users = \App\Models\User::whereIn('id', array_column($user->notifications->pluck('data')->toArray(),'user_id'))->withTrashed()->get();
  @endphp
  {{-- Pagination links on top --}}
  {{$user->notifications()->paginate(20)->links()}}
  {{-- Show all notifications --}}
  @forelse($user->notifications()->paginate(20) as $notification)
    @include('default::frontend.notifications.' . snake_case(class_basename($notification->type)))
  @empty
    {{-- Start empty list message --}}
    <div class="empty-list">
      {{-- Icon --}}
      <div class="icon">
        <i class="far fa-frown" aria-hidden="true"></i>
      </div>
      {{-- Text --}}
      <div class="text">
        {{ trans('notifications.no_notifications') }}
      </div>
    </div>
    {{-- End empty list message --}}
  @endforelse
  {{-- Pagination links on bottom --}}
  {{$user->notifications()->paginate(20)->links()}}

@stop


@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){

  $('a[data-notif-id]').click(function () {
    {{-- Get notification id --}}
    var notif_id   = $(this).data('notifId');
    {{-- Get target after click --}}
    var targetHref = $(this).attr('href');

    $.ajax({
        url:'{{ url('dash/notifications/read') }}',
        type:'POST',
        data:{'notif_id': notif_id},
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        success:function(data){
          window.location.href = targetHref;
        },
        error: function (data) {
          alert('Error');
        }
    });

    return false;
  });

})
</script>
@stop
