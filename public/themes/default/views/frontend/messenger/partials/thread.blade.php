<li class="list-group-item" id="thread-{{$thread->id}}">
  <div class="flex-center">
    {{-- User Avatar --}}
    <span class="avatar @if($thread->otherParticipant()->isOnline()) avatar-online @else avatar-offline @endif m-r-10 no-flex-shrink">
      <img src="{{$thread->otherParticipant()->avatar_square_tiny }}" alt="{{$thread->otherParticipant()->name}}'s Avatar"><i></i>
      {{-- Unread messages count --}}
      @php $unread = $thread->userUnreadMessages(Auth::id())->count(); @endphp
      {{-- Show badge if user have unread messages in this thread --}}
      @if($unread>0)
        <span id="badge-{{$thread->id}}" class="badge badge-danger badge-sm up">{{$unread}}</span>
      @endif
    </span>
    {{-- User Name & Location --}}
    <div class="flex-overflow-fix" style="width: 100%;">
      {{-- User Name --}}
      <span class="profile-name">
        {{ $thread->otherParticipant()->name }}
      </span>
      <div class="flex-center-space">
        {{-- Last message from this thread --}}
        <div class="last-message">
        {{ substr($thread->latestMessage->body,0, 50) }}
        </div>
        {{-- Last message created at --}}
        <div class="last-time">{{ $thread->latestMessage->created_at->diffForHumans()  }}</div>
      </div>
    </div>
  </div>
</li>
