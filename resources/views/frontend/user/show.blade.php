@extends(Theme::getLayout())
@include('frontend.user.subheader')

@section('content')
@yield('user-content')


<!-- Nav tabs -->
<div class="m-b-40 m-t-40 subheader-tabs-wrapper flex-center-space">
  {{-- Start Nav tabs --}}
  <ul class="subheader-tabs" role="tablist">
    <li class="nav-item">
      <a data-toggle="tab" href="#listings" data-target="#listings" role="tab" class="subheader-link">
        <i class="fa fa-tags" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('users.profile.listings') }}</span>
      </a>
    </li>
    @if($user->ratings->count() > 0)
    <li class="nav-item">
      <a data-toggle="tab" href="#ratings" data-target="#ratings" role="tab" class="subheader-link">
        <i class="fa fa-thumbs-up" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('users.profile.ratings') }}</span>
      </a>
    </li>
    @endif
    {{-- Check if logged in user is user --}}
    @if(!(Auth::check() && Auth::user()->id == $user->id))
    <li class="nav-item">
      <a href="javascript:void(0)" data-toggle="modal" data-target="{{ Auth::check() ? '#NewMessage' : '#LoginModal' }}" class="subheader-link">
        <i class="fas fa-envelope-open" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('messenger.send_message') }}</span>
      </a>
    </li>
    @endif
  </ul>
</div>

<div class="tab-content subheader-margin">

  {{-- START LISTINGS --}}
  <div class="tab-pane fade" id="listings" role="tabpanel">
    <div class="row">
      @forelse($listings as $listing)
        {{-- START GAME --}}
        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">

          {{-- Start Game Cover --}}
          <div class="card game-cover-wrapper hvr-grow-shadow"  style="margin-bottom: 0px;">
            {{-- Show "New!" label if item or price is not older than 1 day --}}
            @if(Carbon\Carbon::now()->subDays(1) < $listing->created_at )
              <div class="item-new {{ $listing->game->cover_generator ? 'with-platform' : ''  }}">{{ trans('listings.general.new') }}</div>
            @endif
            {{-- Pacman Loader for background image - show only when cover exists --}}
            @if($listing->game->image_cover)
              {{--
            <div class="loader pacman-loader cover-loader"></div> --}}
            {{-- Show game name, when no cover exist --}}
            @else
            <div class="no-cover-name">{{$listing->game->name}}</div>
            @endif

            <a href="{{ $listing->url_slug }}">

              {{-- Payment icon --}}
              @if($listing->payment)
              <div class="animation-scale-up payment-enabled">
                <i class="fa fa-shield-check" aria-hidden="true"></i>
              </div>
              @endif

              {{-- Digital download icon --}}
              @if($listing->digital)
              <div class="animation-scale-up digital-download {{ $listing->payment ? 'with-payment' : '' }}">
                <i class="fa fa-download" aria-hidden="true"></i>
              </div>
              @endif

              {{-- Pickup icon --}}
              @if($listing->pickup)
              <div class="pickup-icon {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
                <i class="far fa-handshake" aria-hidden="true"></i>
              </div>
              @endif

              {{-- Delivery icon --}}
              @if($listing->delivery)
              <div class="delivery-icon {{ $listing->pickup ? 'with-pickup' : '' }} {{ $listing->digital ? 'with-digital' : '' }} {{ $listing->payment ? 'with-payment' : '' }}">
                <i class="fa fa-truck" aria-hidden="true"></i>
              </div>
              @endif

              {{-- Generated game cover with platform on top --}}
              @if($listing->game->cover_generator)
                <div class="lazy game-cover gen"  data-original="{{$listing->game->image_cover}}"></div>
                <div class="game-platform-gen" style="background-color: {{$listing->game->platform->color}}; text-align: {{$listing->game->platform->cover_position}};">
                  {{-- Check if platform logo setting is enabled --}}
                  @if( config('settings.platform_logo') )
                    <img src="{{ asset('logos/' . $listing->game->platform->acronym . '_tiny.png/') }}" alt="{{$listing->game->platform->name}} Logo">
                  @else
                    <span>{{$listing->game->platform->name}}</span>
                  @endif
                </div>
              {{-- Normal game cover --}}
              @else
                <div class="lazy game-cover"  data-original="{{$listing->game->image_cover}}"></div>
              @endif
              {{-- Item name --}}
              @if($listing->game->image_cover)
              <div class="item-name">
                {{ $listing->game->name }} @if($listing->limited_edition)<span><i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>@endif
              </div>
              @elseif($listing->limited_edition)
              <div class="item-name">
                <i class="fa fa-star" aria-hidden="true"></i> {{ $listing->limited_edition }}<span>
              </div>
              @endif
              @if($listing->picture)
              <div class="lazy item-image" data-original="{{ $listing->picture_square }}"></div>
              @endif
            </a>
          </div>
          {{-- End Game Cover --}}


          <div class="listing-details flex-center-space" style="margin-top: 5px;">
            @if($listing->sell)
            <div class="listing-price">
              {{ $listing->getPrice() }}
            </div>
            @else
            <div>
            </div>
            @endif
            @if($listing->trade)
            <div class="listing-trade @if($listing->sell) with-price @endif" class="no-flex-shrink">
                <i class="fa fa-exchange"></i>
            </div>
            @endif
          </div>
        </div>
        {{-- End GAME --}}
    @empty
      <div class="no-listings">
        {{-- Start empty list message --}}
        <div class="empty-list">
          {{-- Icon --}}
          <div class="icon">
            <i class="far fa-frown" aria-hidden="true"></i>
          </div>
          {{-- Text --}}
          <div class="text">
            {{ trans('listings.general.no_listings') }}
          </div>
        </div>
        {{-- End empty list message --}}
      </div>
    @endforelse

    </div>
  {{-- END LISTINGS --}}
  {{ $listings->links() }}
  </div>

  {{-- START RATINGS --}}
  <div class="tab-pane fade" id="ratings" role="tabpanel">
    @forelse ($ratings as $rating)
      @php
        if($rating->rating == 2){
          $bg = 'bg-success';
          $icon = 'fa-thumbs-up';
        }else if($rating->rating == 1){
          $bg = 'bg-dark';
          $icon = 'fa-minus';
        }else{
          $bg = 'bg-danger';
          $icon = 'fa-thumbs-down';
        }
      @endphp
      {{-- Start Rating --}}
      <section class="panel rating-panel hvr-grow-shadow2 {{$bg}}">
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
        <div class="background-color" style="border-radius: 5px;"></div>

        <div class="panel-body">
          {{-- Rating icon --}}
          <i class="fa {{$icon}} rating-icon" aria-hidden="true"></i>
          {{-- User avatar --}}
          <span class="avatar">
            <img src="{{$rating->user_from->avatar_square}}" alt="{{$rating->user_from->name}}'s Avatar">
          </span>
          {{-- Notice --}}
          <div>
            <span class="from-user">{{ trans('users.profile.rating_from', ['username' => $rating->user_from->name]) }}</span>
            {{-- Rating notice --}}
            @if($rating->notice)
              {{-- Head text with username from rater--}}
              <span class="notice"><i class="fa fa-quote-left" aria-hidden="true"></i> {{$rating->notice}} <i class="fa fa-quote-right" aria-hidden="true"></i></span>
            @else
              {{-- No notice --}}
              <span class="notice">{{ trans('offers.status_complete.no_notice') }}</span>
            @endif
          </div>

        </div>
      </section>
      {{-- End Rating --}}
    @empty
      <div style="text-align: center;">
        {{-- No Ratings --}}
        <div style="text-align: center; display: block;">
          <span class="fa-stack fa-lg" style="font-size: 50px;text-align: center;">
            <i class="fa fa-thumbs-up fa-stack-1x"></i>
            <i class="fa fa-ban fa-stack-2x text-danger"></i>
          </span>
        </div>
        <span class="no-ratings">{{ trans('users.general.no_ratings') }}</span>
      </div>
    @endforelse
  </div>
  {{-- END RATINGS --}}

  {{-- Start Edit / Delete when user has permission --}}
  @if(Auth::check() && Auth::user()->can('edit_users'))
  <div>
    @if($user->isActive())
      <a href="{{ url(config('backport.route.prefix', 'admin') . '/users/' . $user->id . '/ban') }}" class="btn btn-danger m-r-5"><i class="fa fa-trash"></i> Ban</a>
    @else
      <a href="{{ url(config('backport.route.prefix', 'admin') . '/users/' . $user->id . '/ban') }}" class="btn btn-success m-r-5"><i class="fa fa-check-circle"></i> Unban</a>
    @endif
    <a href="{{ url(config('backport.route.prefix', 'admin') . '/users/' . $user->id . '/edit') }}" class="btn btn-dark" target="_blank"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>
  </div>
  @endif

</div>

{{-- Include new message modal --}}
{{-- Check if logged in user is user --}}
@if(!(Auth::check() && Auth::user()->id == $user->id))
  @include('frontend.messenger.partials.modal-message')
@endif

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('profile', $user) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')


<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>


<script type="text/javascript">
$(document).ready(function(){

  {{-- Javascript to enable link to tab --}}
  var hash = document.location.hash;
  var prefix = "!";
  if (hash) {
      hash = hash.replace(prefix,'');
      var hashPieces = hash.split('?');
      activeTab = $('[role="tablist"] [data-target="' + hashPieces[0] + '"]');
      activeTab && activeTab.tab('show');

      var $this = activeTab,
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');


      if( !$.trim( $(targ).html() ).length ){

        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }


  }else{
      activeTab = $('[role="tablist"] [data-target="#listings"]');
      activeTab && activeTab.tab('show');
  }

  {{-- Change hash for page-reload --}}
  $('[role="tablist"] a').on('shown.bs.tab', function (e) {
      var $this = $(this),
      loadurl = $this.attr('href'),
      targ = $this.attr('data-target');

      if( !$.trim( $(targ).html() ).length ){

        $.ajax({
            url: loadurl,
            type: 'GET',
            beforeSend: function() {
                // TODO: show your spinner
                $('#loading').show();
            },
            complete: function() {
                // TODO: hide your spinner
                $('#loading').hide();
            },
            success: function(result) {
              $(targ).html(result);
            }
        });


      }


      window.location.hash = targ.replace("#", "#" + prefix);


  });
});
</script>
@endsection


@stop
