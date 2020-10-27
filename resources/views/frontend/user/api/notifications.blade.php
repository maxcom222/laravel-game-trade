{{-- Load data for notifications --}}
@php
$listings = \App\Models\Listing::whereIn('id', array_column($user->unreadNotifications->pluck('data')->toArray(),'listing_id'))->withTrashed()->get();
$listings->load('game','game.platform','game.giantbomb');
$offers = \App\Models\Offer::whereIn('id', array_column($user->unreadNotifications->pluck('data')->toArray(),'offer_id'))->withTrashed()->get();
$offers->load('game','user');
$users = \App\Models\User::whereIn('id', array_column($user->notifications->pluck('data')->toArray(),'user_id'))->withTrashed()->get();
@endphp
{{-- Show all notifications --}}
@forelse($user->unreadNotifications()->paginate(5) as $notification)
  @include('default::frontend.notifications.' . snake_case(class_basename($notification->type)))
@empty
  <li class="dropdown-notifications-loading">
    <div>
      <span class="fa-stack fa-lg">
        <i class="fa fa-bell fa-stack-1x"></i>
        <i class="fa fa-ban fa-stack-2x"></i>
      </span>
    </div>
    {{ trans('notifications.no_unread_notifications') }}
  </li>
@endforelse
@if($user->unreadNotifications()->count() > 5)
  <li class="dropdown-notifications-loading more">
    {{ trans('general.nav.user.notifications_more', ['count' => $user->unreadNotifications()->count() - 5]) }}
  </li>
@endif

<script type="text/javascript">
$(document).ready(function(){

  {{-- Mark notification as read on click --}}
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
