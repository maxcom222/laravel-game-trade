{{-- Start Modal for new messages --}}
<div class="modal fade modal-fade-in-scale-up modal-danger modal-wishlist" id="{{ isset($game->wishlist) ? 'EditWishlist_' . $game->wishlist->id : 'AddWishlist' }}" tabindex="-1" role="dialog">
  <div class="modal-dialog user-dialog">
    <div class="modal-content">
      {{-- Modal Header --}}
      <div class="modal-header">
        {{-- Background pattern --}}
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        {{-- Background color --}}
        <div class="background-color"></div>
        {{-- Modal title --}}
        <div class="title">
          {{-- Close button --}}
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
          </button>
          {{-- Title --}}
          <h4 class="modal-title">
            <i class="fas fa-heart m-r-5"></i>
            @if(isset($game->wishlist))
              {{-- Trans: Update Wishlist --}}
              <strong>{{ trans('wishlist.update_wishlist') }}</strong>
            @else
              {{-- Trans: Add to Wishlist --}}
              <strong>{{ trans('wishlist.add_wishlist') }}</strong>
            @endif
          </h4>
        </div>
      </div>

      {{-- Open new form for adding a new message --}}
      {!! Form::open(array('url'=> isset($game->wishlist) ? $game->url_slug . '/wishlist/update' : $game->url_slug . '/wishlist/add' , 'id'=>'form-new-wishlist' . (isset($game->wishlist) ? '-' . $game->wishlist->id : ''), 'role'=>'form')) !!}

      {{-- Start selected game panel --}}
      <div class="selected-game flex-center">
        {{-- Game cover --}}
        <div>
          <span class="avatar m-r-10"><img src="{{ $game->image_square_tiny}}" /></span>
        </div>
        {{-- Game title and platform --}}
        <div>
          <span class="selected-game-title">
            <strong>{{$game->name}}</strong>@if($game->release_date)<span class="release-year m-l-5">{{$game->release_date->format('Y')}}</span>@endif
          </span>
          <span class="platform-label" style="background-color:{{$game->platform->color}}; ">
            {{$game->platform->name}}
          </span>
        </div>
      </div>
      {{-- End selected game panel --}}

      <div class="modal-body">
        <div class="main-content">
          {{-- Checkbox for send notifcations --}}
          <div class="checkbox-custom checkbox-default checkbox-lg" >
            <input type="checkbox" id="wishlist-notification{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}" name="wishlist-notification" {{ isset($game->wishlist) && $game->wishlist->notification ? 'checked' : ''}}>
            <label for="wishlist-notification{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}">
              {{-- Trans: Send Notification --}}
              <i class="fas fa-bell"></i> {{ trans('wishlist.modal.send_notification') }}
            </label>
          </div>
          {{-- Maximum Price --}}
          <div id="max-price{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}" class="max-price {{ isset($game->wishlist) && $game->wishlist->notification ? '' : 'hidden' }}">
          {{-- Trans: Maximum Price --}}
          <label class="f-w-700">{{ trans('wishlist.modal.maximum_price') }}</label>
            {{-- Field for maximum price --}}
            <div class="input-group">
              <span class="input-group-addon">
                {{ Currency(Config::get('settings.currency'))->getSymbol() }}
              </span>
              <input type="text" class="form-control rounded input-lg inline input wishlist_price" name="wishlist_price" autocomplete="off" id="wishlist_price" value="{{(isset($game->wishlist) && $game->wishlist->max_price != 0 ? $game->wishlist->getMaxPrice(false) : null)}}" />
            </div>
            <span class="text-xs">
              {{-- Trans: Leave blank if you want to get a notification for each :Game_name listing. --}}
              <i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('wishlist.modal.maximum_price_hint', ['game_name' => $game->name]) }}
            </span>
          </div>
        </div>
      </div>
      {!! Form::close() !!}
      {{-- Close new form for adding a new message --}}

      <div class="modal-footer">
        {{-- Close button --}}
        <a href="javascript:void(0)" data-dismiss="modal" data-bjax class="btn btn-dark btn-lg">{{ trans('listings.modal.close') }}</a>
        {{-- Submit button --}}
        <button id="send-wishlist{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}" class="btn btn-danger btn-animate btn-animate-vertical btn-lg" type="submit">
          @if(isset($game->wishlist))
            {{-- Trans: Update Wishlist --}}
            <span><i class="icon fas fa-heart" aria-hidden="true"></i> {{ trans('wishlist.update_wishlist') }}</span>
          @else
            {{-- Trans: Add to Wishlist --}}
            <span><i class="icon fas fa-heart" aria-hidden="true"></i> {{ trans('wishlist.add_wishlist') }}</span>
          @endif
        </button>
      </div>

    </div>
  </div>
</div>
{{-- End Modal for for new messages --}}


@push('scripts')
<script src="{{ asset('js/autoNumeric.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
  {{-- Wishlist submit --}}
  $("#send-wishlist{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}").click( function(){
    $('#send-wishlist{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }} span').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#send-wishlist{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}').addClass('loading');
    $('#form-new-wishlist{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}').submit();
  });

  {{-- Start mask prices for money input --}}
  const autoNumericOptions = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
  };

  {{-- Initialization --}}
  $('.wishlist_price').autoNumeric('init', autoNumericOptions);

  {{-- Open maximal price input if notifications are enabled --}}
  $('#wishlist-notification{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}').click(function() {
    if( $(this).is(':checked')) {
      $("#max-price{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}").slideDown('fast');
    } else {
      $("#max-price{{ isset($game->wishlist) ? '-' . $game->wishlist->id : '' }}").slideUp('fast');
    }
  });

});
</script>
@endpush
