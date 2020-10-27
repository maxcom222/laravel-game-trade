{{-- Start modal for balance payment --}}
<div class="modal fade modal-fade-in-scale-up modal-success modal-payment" id="PaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

        <div class="title">
          {{-- Close Modal --}}
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
          </button>
          {{-- Modal title (Pay :total) --}}
          <h4 class="modal-title" id="myModalLabel">
            <i class="far fa-money-bill" aria-hidden="true"></i>
            {{ trans('payment.transaction.pay_now', ['total' => money((($offer->price_offer != $listing->price ? $offer->price_offer : $listing->price) + $listing->delivery_price), config('settings.currency'))->format(true)]) }}
          </h4>
        </div>

      </div>
      {!! Form::open(array('url'=> 'offer/pay/balance', 'id'=>'form-payment', 'role'=>'form')) !!}
      <div class="modal-body">
        {{-- Available Balance --}}
        <div class="balance flex-center-space">
          <div>
            {{ trans('payment.available_balance') }}
          </div>
          <div class="price available">
            {{ money(abs(filter_var(number_format( Auth::user()->balance,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}
          </div>
        </div>
        <div class="total">
          <div class="total-seperator border flex-center-space">
            <div>
              {{-- Start Game --}}
              <div class="selected-game flex-center">
                {{-- Game cover --}}
                <div>
                  <span class="avatar m-r-10"><img src="{{ $game->image_square_tiny}}" /></span>
                </div>
                {{-- Game title and platform --}}
                <div>
                  <span class="selected-game-title">
                    <strong class="f-w-700">{{$game->name}}</strong><span class="platform-label m-l-5" style="background-color:{{$game->platform->color}}; ">{{$game->platform->name}}</span>
                  </span>
                  <span>{{ trans('listings.general.condition') .': '. $listing->condition_string . ' - ' .  trans('payment.sold_by', ['username' => $listing->user->name, 'country' => $listing->user->location->country_abbreviation,'place' => $listing->user->location->place]) }}</span>
                </div>
              </div>
              {{-- End Game --}}
            </div>
            {{-- Game price --}}
            <div class="price">
                {{ $offer->price_offer != $listing->price ? $offer->price_offer_formatted : $listing->price_formatted }}
            </div>
          </div>
          {{-- Delivery --}}
          @if($listing->delivery)
          <div class="total-seperator border flex-center-space">
            <div>
              {{ trans('listings.general.delivery') }}
            </div>
            <div class="price">
              {{ $listing->delivery_price_formatted }}
            </div>
          </div>
          @endif
          {{-- Total --}}
          <div class="total-seperator flex-center-space">
            <div>
              {{ trans('payment.total') }}
            </div>
            <div class="price total-price">
              {{ money((($offer->price_offer != $listing->price ? $offer->price_offer : $listing->price) + $listing->delivery_price), config('settings.currency'))->format(true) }}
            </div>
          </div>
        </div>
        {{-- Remaining Balance --}}
        <div class="balance flex-center-space">
          <div>
            {{ trans('payment.remaining_balance') }}
          </div>
          <div class="price">
            {{ money(abs(filter_var(number_format( Auth::user()->balance,2), FILTER_SANITIZE_NUMBER_INT)) - (($offer->price_offer != $listing->price ? $offer->price_offer : $listing->price) + $listing->delivery_price), config('settings.currency'))->format(true) }}
          </div>
        </div>
        <div class="hold-info m-t-10">
          <i class="fas fa-info-circle"></i> {{ trans('payment.hold_info', ['gamename' => $listing->game->name , 'username' => $listing->user->name]) }}
        </div>
      </div>

      <div class="modal-footer">
        {{-- Cancel button --}}
        <a href="#" data-dismiss="modal" data-bjax class="btn btn-lg btn-dark btn-animate btn-animate-vertical">
          <span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }}</span>
        </a>
        <input name="offer_id" type="hidden" value="{{ encrypt($offer->id) }}">
        {{-- Submit button --}}
        &nbsp;<a class="btn btn-lg btn-success btn-animate btn-animate-vertical" id="payment-submit" href="javascript:void(0)">
          <span><i class="icon far fa-money-bill" aria-hidden="true"></i> {{ trans('payment.transaction.pay_now', ['total' => money((($offer->price_offer != $listing->price ? $offer->price_offer : $listing->price) + $listing->delivery_price), config('settings.currency'))->format(true)]) }}
          </span>
        </a>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
{{-- End modal for balance payment --}}
