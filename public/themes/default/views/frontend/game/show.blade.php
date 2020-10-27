@extends(Theme::getLayout())

{{-- Add Game Subheader --}}
@include('frontend.game.subheader')

@section('content')
{{-- Content from Subheader --}}
@yield('game-content')

{{-- Start Subheader tabs --}}
<div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xxl-10">
  {{-- Start Item Content --}}
  <div class="item-content">
    <div class="subheader-tabs-wrapper flex-center-space">
      {{-- Nav tabs --}}
      <div class="no-flex-shrink">
        <ul class="subheader-tabs" role="tablist">
          {{-- Listings tab --}}
          <li class="nav-item">
            <a data-toggle="tab" href="#listings" data-target="#listings" role="tab" class="subheader-link">
              <i class="fa fa-tags" aria-hidden="true"></i><span class="hidden-xs-down"> {{ trans('listings.general.listings') }}</span>
            </a>
          </li>
          {{-- Trade tab --}}
          @if(count($game->tradegames)>0)
          <li class="nav-item">
            <a data-toggle="tab" href="{{ url('/games/' . $game->id . '/trade') }}" data-target="#trade" role="tab" class="subheader-link">
              <i class="fa fa-exchange" aria-hidden="true"></i><span class="{{ config('settings.comment_listing') ? 'hidden-sm-down' : 'hidden-xs-down'}}"> {{ trans('listings.general.trade') }}</span>
            </a>
          </li>
          @endif
          {{-- Media tab (Images & Videos) --}}
          @if($game->giantbomb_id)
          <li class="nav-item">
            <a data-toggle="tab" href="{{ url('/games/' . $game->id . '/media') }}" data-target="#media" role="tab" class="subheader-link">
              <i class="fa fa-images" aria-hidden="true"></i><span class="{{ config('settings.comment_game') ? 'hidden-sm-down' : 'hidden-xs-down'}}"> {{ trans('games.overview.subheader.media') }}</span>
            </a>
          </li>
          @endif
          {{-- Comments tab --}}
          @if(config('settings.comment_game'))
          <li class="nav-item">
            <a data-toggle="tab" href="#comments" data-target="#comments" role="tab" class="subheader-link">
              <i class="fa fa-comments" aria-hidden="true"></i><span class="{{ config('settings.comment_game') ? 'hidden-sm-down' : 'hidden-xs-down'}}"> {{ trans('comments.comments') }}</span>
            </a>
          </li>
          @endif
        </ul>
      </div>
      {{-- Share buttons --}}
      <div @if(config('settings.comment_game')) class="subheader-social-comments" @endif>
        {{-- Facebook share --}}
        <a href="https://www.facebook.com/dialog/share?
    app_id={{config('settings.facebook_client_id')}}&display=popup&href={{URL::current()}}&redirect_uri={{ url('self.close.html')}}" onclick="window.open(this.href, 'facebookwindow','left=20,top=20,width=600,height=400,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-facebook m-r-5">
          <i class="icon fab fa-facebook-f" aria-hidden="true"></i>
        </a>
        {{-- Twitter share --}}
        <a href="http://twitter.com/intent/tweet?text={{trans('general.share.twitter_game', ['game_name' => $game->name, 'platform' => $game->platform->name, 'page_name' => config('settings.page_name')])}} &#8921; {{URL::current()}}" onclick="window.open(this.href, 'twitterwindow','left=20,top=20,width=600,height=300,toolbar=0,resizable=1'); return false;" class="btn btn-icon btn-round btn-lg social-twitter m-r-5">
          <i class="icon fab fa-twitter" aria-hidden="true"></i>
        </a>
      </div>
    </div>
    {{-- End Subheader tabs --}}

    {{-- Start tabs content --}}
    <div class="tab-content subheader-margin m-t-40">

      {{-- Load Google AdSense --}}
      @if(config('settings.google_adsense'))
        @include('frontend.ads.google')
      @endif

      @if(count($game->tradegames)>0)
      {{-- Start trade tab --}}
      <div class="tab-pane fade" id="trade" role="tabpanel">
      </div>
      {{-- End trade tab --}}
      @endif

      @if(config('settings.comment_game'))
      {{-- Start comments tab --}}
      <div class="tab-pane fade" id="comments" role="tabpanel">
        @php $item_type = 'game'; $item_id = $game->id; @endphp
        @include('frontend.comments.form')
      </div>
      {{-- End comments tab --}}
      @endif

      {{-- Start media tab --}}
      <div class="tab-pane fade" id="media" role="tabpanel">
      </div>
      {{-- End media tab --}}

      {{-- Start listings tab --}}
      <div class="tab-pane fade" id="listings" role="tabpanel">

      {{-- Start Listings --}}
      @forelse($game->listings as $listing)
      @php $trade_list = json_decode($listing->trade_list); @endphp
        {{-- Start Listing Details --}}
        <div class="listing hvr-grow-shadow2">

          {{-- Sell details (price) for listing --}}
          @if($listing->sell == 1)
            {{-- Secure payment badge --}}
            @if($listing->payment)
            <div class="secure-payment-details">
              <i class="fa fa-shield-check" aria-hidden="true"></i>
            </div>
            @endif
          <div class="sell-details">
            {{ $listing->getPrice() }}
          </div>
          @endif
          {{-- Show trade icon when user accept tradde --}}
          @if($listing->trade == 1)
          <div class="trade-details">
            <i class="fa fa-exchange"></i>
          </div>
          @endif
          {{-- Show listing details --}}
          <div class="listing-detail-wrapper">
            <div class="listing-detail">
              {{-- Digital Download --}}
              @if($listing->digital)
              <div class="value condition">
                <div class="value-title">
                  {{ trans('listings.general.digital_download') }}
                </div>
                <div class="text">
                  {{$listing->game->platform->digitals->where('id',$listing->digital)->first()->name}}
                </div>
              </div>
              @else
              {{-- Condition --}}
              <div class="value condition">
                <div class="value-title">
                  {{ trans('listings.general.condition') }}
                </div>
                <div class="text">
                  {{$listing->condition_string}}
                </div>
              </div>
              @endif
              {{-- Pickup --}}
              <div class="value pickup">
                <div class="value-title">
                  {{ trans('listings.general.pickup') }}
                </div>
                @if($listing->pickup == 1)
                  <div class="vicon">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                  </div>
                @else
                  <div class="vicon disabled">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                  </div>
                @endif
              </div>
              {{-- Delivery --}}
              <div class="value">
                <div class="value-title">
                  {{ trans('listings.general.delivery') }}
                </div>
                @if($listing->delivery)
                  <div class="vicon">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                  </div>
                @else
                  <div class="vicon disabled">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                  </div>
                @endif
              </div>
              {{-- Limited Edition --}}
              @if($listing->limited_edition)
              <div class="value limited-edition condition">
                <div class="value-title">
                  {{ trans('listings.form.details.limited') }}
                </div>
                <div class="text">
                  {{$listing->limited_edition}}
                </div>
              </div>
              @endif

              {{-- Trade list --}}
              @if(!is_null($listing->trade_list))
              <div class="trade-list">
                @php $trade_list = App\Models\Game::whereIn('id', array_keys(json_decode($listing->trade_list, true)))->with('platform')->get(); @endphp
                {{-- Get additional charges --}}
                @php $add_charge = json_decode($listing->trade_list,true); @endphp
                @foreach($trade_list as $trade_game)
                  {{-- Trade game with popover --}}
                  <a href="javascript:void(0);" data-toggle="popover" data-html="true" data-placement="top" data-content='<span class="platform-label" style="background-color: {{ $trade_game->platform->color }};">{{ $trade_game->platform->name }}</span>@if($add_charge[$trade_game->id]['price_type'] != 'none') <span class="m-l-5 charge-label {{ $add_charge[$trade_game->id]['price_type'] == 'want' ? 'bg-danger' : 'bg-success'}}">{{ money($add_charge[$trade_game->id]['price'], Config::get('settings.currency')) }}</span> @endif' data-title='{{ $trade_game->name }}'>
                    <span class="avatar gray hvr-grow-shadow3 m-r-5">
                      <img src="{{$trade_game->image_square_tiny}}" style="box-shadow: 0px 0px 0px 2px {{ $trade_game->platform->color }};">
                    </span></a>
                @endforeach

              </div>
              @endif

            </div>
          </div>

          {{-- Details Button --}}
          <a href="{{ $listing->url_slug }}">
            <div class="details-button">
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
              <span class="hidden-sm-down"> {{ trans('listings.overview.subheader.details') }}</span>
            </div>
          </a>
        </div>
        {{-- End Listing Details --}}
        {{-- Start user info and creation date --}}
        <div class="listing-user-details flex-center-space">
          <div>
            <a href="{{$listing->user->url}}" class="user-link">
              <span class="avatar avatar-xs @if($listing->user->isOnline()) avatar-online @else avatar-offline @endif">
                <img src="{{ $listing->user->avatar_square_tiny }}" alt="{{$listing->user->name}}'s Avatar"><i></i>
              </span>
              {{$listing->user->name}}
            </a> {{$listing->created_at->diffForHumans()}}
          </div>
          <div class="no-flex-shrink">
            <span class="profile-location small">
            @if($listing->user->location)
              {{$listing->user->location->place}} <img src="{{ asset('img/flags/' .   $listing->user->location->country_abbreviation . '.svg') }}" height="14"/>@if($listing->distance !== false)<i class="fa fa-location-arrow m-l-10" aria-hidden="true"></i> {{$listing->distance}} {{config('settings.distance_unit')}}@endif
            @endif
            </span>
          </div>
        </div>
        {{-- End user info and creation date --}}
      @empty
        {{-- Start empty list message --}}
        <div class="no-listings game-overview">
          <div class="empty-list add-button">
            {{-- Icon --}}
            <div class="icon">
              <i class="far fa-frown" aria-hidden="true"></i>
            </div>
            {{-- Text --}}
            <div class="text">
              {{ trans('listings.general.no_listings') }}
            </div>
            {{-- Create listing button --}}
            @if(Auth::check())
            <a href="{{ url('listings/' . str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id . '/add' ) }}" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
            @else
            <a href="javascript:void(0);" data-toggle="modal" data-target="#LoginModal" class="btn btn-orange"><i class="fa fa-plus" aria-hidden="true"></i> {{ trans('listings.general.no_listings_add') }}</a>
            @endif
          </div>
        </div>
        {{-- End empty list message --}}
        @endforelse
        {{-- End Listings --}}


        {{-- Site Action for adding new listing --}}
        <div class="site-action">
          @if(Auth::check())
          <button type="button" onclick="location.href='{{ url('listings/' . str_slug($game->name) . '-' . $game->platform->acronym . '-' . $game->id . '/add' ) }}';" class="site-action-toggle btn-raised btn btn-orange btn-floating animation-scale-up">
            <i class="front-icon fa fa-plus" aria-hidden="true"></i>
          </button>
          @else
          <button type="button" data-toggle="modal" data-target="#LoginModal" class="site-action-toggle btn-raised btn btn-orange btn-floating animation-scale-up">
            <i class="front-icon fa fa-plus" aria-hidden="true"></i>
          </button>
          @endif
        </div>
        {{-- End Site Action --}}

      </div>
      {{-- End listings tab --}}

      {{-- Admin quick toggles - no translation, it's just for the admin --}}
      @can('edit_games')

      <div class="form-inline m-t-50">
        {{-- Edit game (redirect to admin panel) --}}
        <a href="{{ url(config('backport.route.prefix', 'admin') . '/games/' . $game->id . '/edit') }}" class="btn btn-dark m-r-5 m-t-10" target="_blank"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a>
        @if(isset($game->metacritic))
        {{-- Refresh metacritic data --}}
        <a href="{{ url('games/' . $game->id . '/refresh/metacritic') }}" class="btn btn-dark m-r-5 m-t-10" id="refresh-metacritic"><i class="fa fa-sync"></i> Refresh Metacritic</a>
        @endif
        {{-- Change giantbomb id --}}
        {!! Form::open(array('url' => 'games/change/giantbomb', 'class' => 'form-inline', 'style' => 'display: inline-block;')) !!}
        <div class="input-group m-t-10" style="width: 300px !important;">
          <div class="input-group-btn">
            <button type="submit" id="change-giantbomb" class="btn btn-dark">
                <i class="fa fa-level-up"></i> Change Giantbomb ID
            </button>
          </div>
          {!! Form::hidden('game_id', encrypt($game->id)) !!}
          <input type="text" name="giantbomb_id" class="form-control input" placeholder="Giantbomb ID" value="{{$game->giantbomb_id}}" style="height: auto;">
        </div>
        {!! Form::close() !!}
      </div>

      @endcan

    </div>
    {{-- End tabs content --}}
  </div>
</div>
</div>

{{-- Include modal for wishlist --}}
@include('frontend.wishlist.inc.modal-wishlist')

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('game', $game) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')


<script src="//cdnjs.cloudflare.com/ajax/libs/masonry/4.1.1/masonry.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>

{{-- Load comment script --}}
@if(config('settings.comment_game'))
  @yield('comments-script')
@endif

<script type="text/javascript">
$(document).ready(function(){


  @can('edit_games')
    {{-- Metacritic refresh submit --}}
    $('#refresh-metacritic').click( function(){
      $('#refresh-metacritic').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Refreshing');
      $('#refresh-metacritic').addClass('loading');
    });

    {{-- Change giantbomb submit --}}
    $('#change-giantbomb').click( function(){
      $('#change-giantbomb').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Fetching new data');
      $('#change-giantbomb').addClass('loading');
      $('#change-giantbomb').submit();
    });
  @endcan

  {{-- Popover for trade list games with grayscale animation --}}
  $('[data-toggle="popover"]').popover({
      html: true,
      trigger: 'manual',
      placement: 'top',
      offset: '10px 3px',
      template: '<div class="popover trade-list-game"><div class="popover-arrow"></div><h3 class="popover-title" role="tooltip"></h3><div class="popover-content"></div></div>'
  }).on("mouseenter", function () {
    $(this).popover('toggle');
      $( ".avatar", this ).removeClass('gray');
  }).on("mouseleave", function () {
    $(this).popover('toggle');
      $( ".avatar", this ).addClass('gray');
  });

  $('[data-toggle="popover"]').popover().click(function(e) {
      $(this).popover('toggle');
      $( ".img-circle", this ).css({'filter': '', 'filter': '', '-webkit-filter': ''});
  });



  {{-- JS to enable links on tab --}}
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

  {{-- Change hash on page reload --}}
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
