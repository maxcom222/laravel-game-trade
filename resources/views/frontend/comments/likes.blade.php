@forelse($likes as $like)
  {{-- Start user like --}}
  <div @if(!$loop->last) class="m-b-10" @endif>
    <a href="{{$like->user->url}}" class="user-link">
      {{-- User avatar --}}
      <span class="avatar avatar-xs @if($like->user->isOnline()) avatar-online @else avatar-offline @endif">
        <img src="{{$like->user->avatar_square_tiny}}" alt="{{$like->user->name}}'s Avatar"><i></i>
      </span>
      {{-- User name --}}
      {{$like->user->name}}
    </a>
  </div>
  {{-- End user likes --}}
@empty
  {{-- No user likes --}}
  <div class="text-center">
    <i class="far fa-frown" aria-hidden="true"></i> {{ trans('comments.no_likes') }}
  </div>
@endforelse
