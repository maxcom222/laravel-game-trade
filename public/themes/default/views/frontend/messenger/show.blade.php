@php
$prev_user = 0;
$o_t = array_diff($thread->participantsUserIds(), array(Auth::user()->id ));
$o_t_last_read = \Cmgmyr\Messenger\Models\Participant::where('thread_id', $thread->id)->where('user_id', reset($o_t))->first()->last_read;
@endphp

{{-- Check if more messages are available --}}
@if($messages->hasMorePages())
  {{-- Button to load new messages --}}
  <a id="load-{{$messages->currentPage()}}" class="btn btn-dark btn-round">{{ trans('general.load_more') }}</a>
  {{-- Wrapper for new messages --}}
  <div id="messages-{{$messages->currentPage()}}"></div>
@endif

@foreach($messages->reverse() as $message)

@php
if(Auth::user()->id == $message->user_id){
    $order = "right";
    $classitem = "bg";
}else{
    $order = "left";
    $classitem = "b";

}
@endphp

{{-- New chat body when previous user is different --}}
@if($prev_user === 0)

  <div class="chat chat-{{ $order }} {{ !$thread->hasParticipant($message->user->id) ? 'staff' : '' }}">
    <div class="chat-avatar">
      <a class="avatar" data-toggle="tooltip" href="{{ $message->user->url }}" data-placement="{{ $order }}" title="{{ $message->user->name }}">
        <img src="{{ $message->user->avatar_square }}" alt="{{ $message->user->name }}'s Avatar">
      </a>
    </div>
    <div class="chat-body">
      <div class="chat-content">
        <div class="text">{{ $message->body }}</div>
        @if($order == 'right')
          <div class="chat-read-badge {{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? '' : 'read' }}" data-toggle="tooltip" data-placement="{{ $order }}" title="{{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? trans('offers.general.chat_sent') : trans('offers.general.chat_read') }}"><i class="fas fa-check-double"></i></div>
        @endif
      </div>
      <div class="clearfix"></div>
      <time class="chat-time" datetime="{{$message->created_at}}"><span style="background-color: #252525; padding: 5px; border-radius: 5px;">{!! $message->created_at->diffForHumans() !!}</span></time>
{{-- Chat content without new body when previous user is same --}}
@elseif($prev_user === $message->user_id)

      <div class="chat-content">
        <div class="text">{{ $message->body }}</div>
        @if($order == 'right')
          <div class="chat-read-badge {{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? '' : 'read' }}" data-toggle="tooltip" data-placement="{{ $order }}" title="{{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? trans('offers.general.chat_sent') : trans('offers.general.chat_read') }}"><i class="fas fa-check-double"></i></div>
        @endif
      </div>
      <div class="clearfix"></div>
        <time class="chat-time" datetime="{{$message->created_at}}"><span style="background-color: #252525; padding: 5px; border-radius: 5px;">{!! $message->created_at->diffForHumans() !!}</span></time>
{{-- New chat body on new user --}}
@else

    </div>
  </div>
  <div class="chat chat-{{ $order }} {{ !$thread->hasParticipant($message->user->id) ? 'staff' : '' }}">
    <div class="chat-avatar">
      <a class="avatar" data-toggle="tooltip" href="{{ $message->user->url }}" data-placement="{{ $order }}" title="{{ $message->user->name }}">
        <img src="{{ $message->user->avatar_square }}" alt="{{ $message->user->name }}'s Avatar">
      </a>
    </div>
    <div class="chat-body">
      <div class="chat-content">
        <div class="text">{{ $message->body }}</div>
        @if($order == 'right')
          <div class="chat-read-badge {{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? '' : 'read' }}" data-toggle="tooltip" data-placement="{{ $order }}" title="{{ ( is_null($o_t_last_read) || $o_t_last_read < $message->created_at ) ? trans('offers.general.chat_sent') : trans('offers.general.chat_read') }}"><i class="fas fa-check-double"></i> </div>
        @endif
      </div>
      <div class="clearfix"></div>
        <time class="chat-time" datetime="{{$message->created_at}}"><span style="background-color: #252525; padding: 5px; border-radius: 5px;">{!! $message->created_at->diffForHumans() !!}</span></time>
@endif

@php $prev_user = $message->user_id; @endphp

@endforeach

{{-- Enable tooltip --}}
<script type="text/javascript">
$(document).ready(function(){
  {{-- Enable tooltips --}}
  $('[data-toggle="tooltip"]').tooltip();
  {{-- Check if more messages are available --}}
  @if($messages->hasMorePages())
  {{-- Load more messages --}}
  function loadMore() {
    {{-- Load URL through AJAX --}}
    $.ajax({
      beforeSend: function () {
        $('.load-progress-animation').removeClass('hide');
        $('.load-progress').css({
          width:'10%'
        });
      },
      {{-- Update progress bar width during loading --}}
      xhr: function () {
        var xhr = new window.XMLHttpRequest();
        {{-- Event listener for loading the URL --}}
        xhr.addEventListener("progress", function (evt) {
          if (evt.lengthComputable) {
            {{-- Get percantage of complete loading --}}
            var percentComplete = evt.loaded / evt.total;
            {{-- Add the complete loading to the loading bar CSS --}}
            $('.load-progress').css({
              width: percentComplete * 100 + '%'
            });
            {{-- Remove loading bar if URL loaded --}}
            if (percentComplete === 1) {
              $('html, body').scrollTop(0);
              $('.load-progress-animation').addClass('hide');
              $('.load-progress').css({
                width: '0%'
              });
            }
          }
        }, false);
        return xhr;
      },
      url: '{{ $messages->nextPageUrl() }}',
      success: function(data) {
        $('#load-{{$messages->currentPage()}}').remove();
        $('#messages-{{$messages->currentPage()}}').html(data);
      }
    });
  }
  $('#load-{{$messages->currentPage()}}').click(function(e){
    e.preventDefault();
    $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
    loadMore();
  });
  @endif
})
</script>
