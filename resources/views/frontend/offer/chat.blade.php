@php
$prev_user = 0;
$o_t = $thread->otherParticipant(Auth::user());
$o_t_last_read = \Cmgmyr\Messenger\Models\Participant::where('thread_id', $thread->id)->where('user_id', $o_t->id)->first()->last_read;
@endphp
@foreach($thread->messages as $message)

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
    $('[data-toggle="tooltip"]').tooltip();
})
</script>
