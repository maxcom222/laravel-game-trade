@extends(Theme::getLayout())

@section('subheader')
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    {{-- Subheader title (Wishlist) --}}
    <div class="content">
      <span class="title"><i class="fa fa-heart"></i> {{ trans('wishlist.wishlist') }}</span>
    </div>

  </div>

@stop


@section('content')
  {{-- Pagination Link before wishlist entries --}}
  {{ $wishlists->links() }}

  @forelse($wishlists as $wishlist)
    {{-- Start Listing --}}
    <section class="panel">

      {{-- Start Listing Header --}}
      <div class="panel-heading listing-heading">
        <div class="flex-center-space">
          <div class="flex-center">
            {{-- Game Cover --}}
            <div class="m-r-10">
              <span class="avatar">
                <img src="{{ $wishlist->game->image_square_tiny }}" alt="{{ $wishlist->game->name }}">
              </span>
            </div>
            {{-- Game Name + platform --}}
            <div>
              <div class="title">{{ $wishlist->game->name }}</div>
              <span class="platform-label" style="background-color:{{ $wishlist->game->platform->color }};"> {{ $wishlist->game->platform->name }} </span>
            </div>
          </div>
          {{-- Max Price set by the user --}}
          <div class="flex-center no-flex-shrink">
            @if($wishlist->max_price || $wishlist->notification)
            <div class="sell-status bg-success">
              @if($wishlist->max_price)
              <span>{{ $wishlist->getMaxPrice() }}</span>
              @endif
              <span class="suggestion"><i class="fas fa-bell" aria-hidden="true"></i></span>
            </div>
            @endif
          </div>
        </div>
      </div>
      {{-- End Listing Header --}}

      <div class="listing-body">
        @forelse($wishlist->listings as $listing)
          @php $trade_list = json_decode($listing->trade_list); @endphp
            {{-- Start Listing Details --}}
            <div class="listing {{ (isset($wishlist->max_price) && $listing->price > $wishlist->max_price) ? 'grayscale' : '' }}">

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
        @empty
        <div class="listing-no-offers">
          <i class="far fa-frown" aria-hidden="true"></i> {{ trans('listings.general.no_listings') }}
        </div>
        @endforelse

      </div>

      {{-- Start Listing Footer --}}

      <div class="panel-footer">
        {{-- Listing created at --}}
        <div class="listing-footer-time">
          {{ $wishlist->created_at->diffForHumans() }} <br/>
          <strong><i class="fa fa-tags"></i> {{ $wishlist->listings->count() }} {{ trans('general.listings') }}</strong>
        </div>
        {{-- Footer Buttons --}}
        <div>
          <a href="{{ $wishlist->game->url_slug }}/wishlist/delete" class="button additional delete-wishlist">
            <i class="fa fa-trash" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.delete') }}</span>
          </a><a href="javascript:void(0);" data-toggle="modal" data-target="#EditWishlist_{{$wishlist->id}}" class="button additional">
            <i class="fa fa-edit" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.edit') }}</span>
          </a><a href="{{ $wishlist->game->url_slug }}" class="button">
            <i class="fa fa-caret-square-right" aria-hidden="true"></i><span class="hidden-sm-down"> {{ trans('general.details') }}</span>
          </a>
        </div>

      </div>
      {{-- End Listing Footer --}}
    </section>
    {{-- End Listing --}}
    {{-- Include modal for wishlist --}}
    @include('frontend.wishlist.inc.modal-wishlist', ['game' => $wishlist->game])
  @empty
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
  @endforelse

  {{-- Pagination Link after wishlist entries --}}
  {{ $wishlists->links() }}


  @section('after-scripts')

  <script type="text/javascript">
  $(document).ready(function(){



    {{-- Delete submit --}}
    $(".delete-wishlist").click( function(){
      $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $(this).addClass('loading');
    });

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

  });
  </script>
  @endsection

@stop
