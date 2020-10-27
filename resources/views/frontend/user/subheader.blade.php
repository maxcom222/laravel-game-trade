@section('subheader')
<div class="subheader-image-bg">
  <div class="bg-image-wrapper">
    {{-- Background image of subheader --}}
    <div class="bg-image" style="background: linear-gradient(0deg, rgba(25,24,24,1) 0%, rgba(25,24,24,1) 30%, rgba(25,24,24,0) 80%), url({{$user->avatar_square}});"></div>
  </div>
  {{-- background color overlay --}}
  <div class="bg-color user-profile {{ !is_null($user->positive_percent_ratings) ? 'with-rating' : '' }}"></div>
</div>


@endsection

@section('user-content')

  <div class="visible-xs-down hidden-sm-up text-center m-b-20" style="margin:0 auto;">
    <span class="avatar profile-avatar {{ $user->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
      <img src="{{$user->avatar_square}}" alt="{{$user->name}}'s Avatar"><i></i>
    </span>
  </div>
  <div class="flex-center-space m-b-20">
    <div class="flex-center">
      {{-- User Avatar --}}
      <span class="hidden-xs-down avatar profile-avatar m-r-20 {{ $user->isOnline() ? 'avatar-online' : 'avatar-offline' }}">
        <img src="{{$user->avatar_square}}" alt="{{$user->name}}'s Avatar"><i></i>
      </span>
      <div>
        {{-- User Name & Location --}}
        <span class="profile-name">
          {{$user->name}}
        </span>
        <span class="profile-location">
        @if($user->location)
          <img src="{{ asset('img/flags/' .   $user->location->country_abbreviation . '.svg') }}" height="16"/> {{$user->location->country_abbreviation}}, {{$user->location->place}} <span class="postal-code">{{$user->location->postal_code}}</span>
        @endif
        </span>
        {{-- Check if user is not banned --}}
        @if($user->status)
          {{-- last activity --}}
          @if($user->last_activity_at)
          <span class="profile-last-seen">
              @if($user->isOnline())
                {{ trans('users.profile.is_online', ['username' => $user->name]) }}
              @else
                {{ trans('users.profile.last_seen', ['date' => $user->last_activity_at->diffForHumans()]) }}
              @endif
          </span>
          @endif
        @else
          {{-- User banned label --}}
          <span class="platform-label bg-danger m-t-10">{{ trans('users.profile.banned') }}</span>
        @endif
      </div>
    </div>
    <div class="rating-wrapper no-flex-shrink">
    {{-- User Ratings --}}
    @if(is_null($user->positive_percent_ratings))
      {{-- No Ratings --}}
      <div class="p-5 flex-center">
        <span class="fa-stack fa-lg">
          <i class="fa fa-thumbs-up fa-stack-1x"></i>
          <i class="fa fa-ban fa-stack-2x text-danger"></i>
        </span>
        <span class="no-ratings hidden-xs-down">{{ trans('users.general.no_ratings') }}</span>
      </div>
    @else
      @php
        if($user->positive_percent_ratings > 70){
          $rating_bg = 'bg-success';
          $rating_icon = 'fa-thumbs-up text-success';
        }else if($user->positive_percent_ratings > 40){
          $rating_bg = 'bg-dark';
          $rating_icon = 'fa-minus';
        }else{
          $rating_bg = 'bg-danger';
          $rating_icon = 'fa-thumbs-down text-danger';
        }
      @endphp
      {{-- Rating in percent --}}
      <div class="p-5">
        <span class="rating-percent"><i class="fa {{$rating_icon}}" aria-hidden="true"></i> {{$user->positive_percent_ratings}}%</span>
      </div>
      {{-- Rating bar --}}
      <div class="rating-bar">
        <span class="{{$rating_bg}} block" style="width: {{$user->positive_percent_ratings}}%;"></span>
      </div>
      {{-- Rating counts --}}
      <div class="rating-counts profile p-5" style="">
        <span class="text-danger"><i class="fa fa-thumbs-down" aria-hidden="true"></i> {{$user->negative_ratings}}</span>&nbsp;&nbsp;
        <i class="fa fa-minus" aria-hidden="true"></i> {{$user->neutral_ratings}}&nbsp;&nbsp;
        <span class="text-success"><i class="fa fa-thumbs-up" aria-hidden="true"></i> {{$user->positive_ratings}}</span>
      </div>
    @endif
    </div>
  </div>

@stop
