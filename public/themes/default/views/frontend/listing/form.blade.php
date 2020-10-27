@extends(Theme::getLayout())

@section('subheader')
{{-- Start Subheader --}}
<div class="subheader">

  <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
  <div class="background-color"></div>

  <div class="content">
    {{-- Title for edit listing --}}
    @if(isset($listing))
      <span class="title"><i class="fa fa-edit"></i> {{ trans('listings.form.edit') }}</span>
    {{-- Title for new listing --}}
    @else
      <span class="title"><i class="fa fa-tag"></i> {{ trans('listings.form.add') }}</span>
    @endif
  </div>

</div>
{{-- End Subheader --}}
@stop

@section('content')
{{-- Show selected game when game is set --}}
@if(isset($game))

  {{-- Start selected game panel --}}
  <section class="panel" id="game">
    <div class="panel-body">
      <div class="flex-center">
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
    </div>
  </section>
  {{-- End selected game panel --}}

{{-- Search when no game is set --}}
@else
  {{-- Start Game Search Panel --}}
  <section class="panel" id="select-game">

    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('listings.form.game.select') }}</h3>
    </div>

    <div class="panel-body">
      {{-- Input group for game search --}}
      <div class="input-group input-group-lg select-game">
        <span class="input-group-addon">
          {{-- Search icon when search is complete --}}
          <span id="listingsearchcomplete">
            <i class="fa fa-search"></i>
          </span>
          {{-- Spin icon when search is in progress --}}
          <span class="hidden" id="listingsearching">
            <i class="fa fa-sync fa-spin"></i>
          </span>
        </span>
        {{-- Input for typeahead --}}
        <input type="text" class="form-control rounded input-lg inline input" id="offersearch" autocomplete="off" placeholder="{{ trans('listings.form.placeholder.game_name') }}">
      </div>
    </div>

    {{-- Check if user can add games to the system --}}
    @if(Config::get('settings.user_add_item'))
    <div class="panel-footer game-add">
      {{-- Link to game add --}}
      <div>
        <span class="text m-r-10">{{ trans('listings.form.game.not_found') }}</span> <a href="{{ url('games/add')  }}" class="add-link"><i class="fa fa-plus"></i> {{ trans('listings.form.game.add') }}</a>
      </div>
    </div>
    @endif

  </section>
  {{-- End Game Search Panel --}}
@endif


{{-- Open form for listing edit --}}
@if(isset($listing))
  {!! Form::open(array('url'=>'listings/edit', 'id'=>'form-listing', 'role'=>'form', 'files' => true )) !!}
  <input name="game_id" type="hidden" value="{{ encrypt($game->id) }}" />
  <input name="listing_id" type="hidden" value="{{ encrypt($listing->id) }}" />
  <input name="redirect" type="hidden" value"{{ url()->previous() }}" />
{{-- Open form for new listing  --}}
@else
  {!! Form::open(array('url'=>'listings/add', 'id'=>'form-listing', 'role'=>'form', 'files' => true )) !!}
  {{-- Set game id when game is already set --}}
  @if(isset($game))
  <input name="game_id" type="hidden" value="{{$game->id}}" />
  @else
  <div class="selected-game"></div>
  @endif
@endif

{{-- Start Listing Form --}}
{{-- Hide listing form when no game is selected --}}
<div class="listing-form {{ isset($game) ? '' : 'hidden' }}">

  {{-- Start Details Panel --}}
  <section class="panel">

    {{-- Panel Title (Details) --}}
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-tag m-r-5"></i>{{ trans('listings.form.details_title') }}</h3>
    </div>

    <div class="panel-body">
      <div class="row no-space">
        <div class="form-group row">
          {{-- Digital Download --}}
          <div class="col-sm-6 {{ isset($game) ? $game->platform->digitals->count() > 0 ? '' : 'hidden' : '' }}" id="digital-input">
            <div class="checkbox-custom checkbox-default checkbox-lg">
              <input type="checkbox" id="digital" name="digital" autocomplete="off" data-platform="{{ isset($game) ? $game->platform->acronym : '' }}" {{ (isset($listing) && $listing->digital) ? 'checked' : '' }} @if(config('settings.digital_downloads_only')) checked disabled @endif />
              <label for="digital">
                {{ trans('listings.form.details.digital') }}
              </label>
            </div>
            {{-- Digital ditributor selection --}}
            <div class="{{ (isset($listing) && $listing->digital) ? '' : (config('settings.digital_downloads_only') ? '' : 'hidden') }}" id="digital-select">
              <select class="form-control select" id="digital_distributor" name="digital_distributor" {{ (isset($listing) && $listing->digital) ? '' : 'disabled' }}>
                {{-- Show digital distributors on edit --}}
                @if(isset($listing) && $listing->digital)
                  @foreach($game->platform->digitals as $digital)
                  <option value="{{$digital->id}}" {{ $digital->id == $listing->digital ? 'selected' : ''}}>{{$digital->name}}
                  </option>
                  @endforeach
                @endif
              </select>
            </div>
          </div>
          {{-- Limited Edition --}}
          <div class="col-sm-6">
            <div class="checkbox-custom checkbox-default checkbox-lg">
              <input type="checkbox" id="limited" name="limited" autocomplete="off" {{ (isset($listing) && $listing->limited_edition) ? 'checked' : '' }} />
              <label for="limited">
                {{ trans('listings.form.details.limited') }}
              </label>
            </div>
            {{-- Limited Edition Input --}}
            <div class="{{ (isset($listing) && $listing->limited_edition) ? '' : 'hidden' }}" id="limited_form">
              <input type="text" placeholder="{{ trans('listings.form.placeholder.limited') }}" class="form-control rounded input-lg inline input" name="limited_name" autocomplete="off" id="limited_name" value="{{(isset($listing) && $listing->limited_edition) ? old('limited_name',$listing->limited_edition) : null }}" />
            </div>
          </div>
        </div>
        {{-- Condition --}}
        @if(!config('settings.digital_downloads_only'))
        <div class="form-group">
          <label>
            {{ trans('listings.general.condition') }} <strong><span class="text-danger">*</span></strong>
          </label>
          <select class="form-control select" id="condition" name="condition" {{ (isset($listing) && $listing->digital) ? 'disabled' : '' }}>
            <option value="5" {{ ( !isset($listing) || (isset($listing) &&  $listing->condition == 5) ? 'selected' : '') }}>{{ trans('listings.general.conditions.5') }}</option>
            <option value="4" {{ ( (isset($listing) &&  $listing->condition == 4) ? 'selected' : '') }}>{{ trans('listings.general.conditions.4') }}</option>
            <option value="3" {{ ( (isset($listing) &&  $listing->condition == 3) ? 'selected' : '') }}>{{ trans('listings.general.conditions.3') }}</option>
            <option value="2" {{ ( (isset($listing) &&  $listing->condition == 2) ? 'selected' : '') }}>{{ trans('listings.general.conditions.2') }}</option>
            <option value="1" {{ ( (isset($listing) &&  $listing->condition == 1) ? 'selected' : '') }}>{{ trans('listings.general.conditions.1') }}</option>
            {{-- Show digital download condition --}}
            @if(isset($listing) && $listing->digital)
            <option value="0" {{ ( (isset($listing) &&  $listing->condition == 0) ? 'selected' : '') }}>{{ trans('listings.general.conditions.0') }}</option>
            @endif
          </select>
        </div>
        @endif
        {{-- Description (Summernote) --}}
        <div class="form-group">
          <label>
            {{ trans('listings.form.details.description') }}
          </label>
          {!! Form::textarea('description', (isset($listing) ? $listing->description : null) ,array('class'=>'form-control input', 'placeholder'=>trans('listings.form.placeholder.description'), 'id' => 'description' )) !!}
        </div>
        {{-- Image Upload (Dropify) --}}
        @if(config('settings.picture_upload') || (isset($listing) && !is_null($listing->picture)))
          <div class="form-group hidden">
            <label>
              {{ trans('listings.form.picture_upload.picture') }}
            </label>
            <input type="file" name="picture" class="dropify" data-height="100" data-allowed-file-extensions="jpg jpeg png" @if(isset($listing) &&  $listing->picture) data-default-file="{{$listing->picture_original}}" @endif />
            <input name="picture_remove" id="picture_remove" type="hidden" value="0">
          </div>
        @endif
        {{-- Delivery & Pickup --}}
        @if(!config('settings.digital_downloads_only'))
        <div class="form-group row">
          <div class="col-xs-12" id="delivery-pickup-error-dialog"></div>
          {{-- Pickup --}}
          <div class="col-sm-6">
            <div class="checkbox-custom checkbox-default checkbox-lg">
              <input type="checkbox" id="pickup" name="pickup" data-validation="delivery_pickup_check" data-validation-event="click" data-validation-error-msg-container="#delivery-pickup-error-dialog" {{ ( isset($listing) &&  $listing->pickup == 1 ? 'checked' : '') }} />
              <label for="pickup">
                <i class="far fa-handshake" aria-hidden="true"></i> {{ trans('listings.general.pickup') }}
              </label>
            </div>
          </div>
          {{-- Delivery --}}
          <div class="col-sm-6">
            <div class="checkbox-custom checkbox-default checkbox-lg" >
              <input type="checkbox" id="delivery" name="delivery" data-validation="delivery_pickup_check" data-validation-event="click"  data-validation-error-msg-container="#delivery-pickup-error-dialog" {{ (!isset($listing) || (isset($listing) &&  $listing->delivery == 1) ? 'checked' : '') }}>
              <label for="delivery">
                <i class="fa fa-truck" aria-hidden="true"></i> {{ trans('listings.general.delivery') }}
              </label>
            </div>
            {{-- Delivery costs --}}
            <div class=" {{ (!isset($listing) || (isset($listing) &&  $listing->delivery == 1) ? '' : 'hidden') }}" id="delivery_cost">
              {{-- Input for delivery costs --}}
              <div class="input-group">
                <span class="input-group-addon">
                  {{ Currency(Config::get('settings.currency'))->getSymbol() }}
                </span>
                <input type="text" placeholder="{{ trans('listings.form.placeholder.delivery') }}" class="form-control rounded input-lg inline input" name="delivery_price" autocomplete="off" id="delivery_price" value="{{(isset($listing) && $listing->delivery_price != 0 ? old('price',$listing->getDeliveryPrice(false)) : null)}}" />
              </div>
              {{-- Delivery Price Info --}}
              <span class="m-b-none text-uc text-xs">
                <i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('listings.form.details.delivery_info') }}
              </span>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>

  </section>
  {{-- End Details Panel --}}

@if(config('settings.picture_upload') || (isset($listing) && !is_null($listing->picture)))
  {{-- Start Images Panel --}}
  <section class="panel">

    {{-- Panel Title (Images) --}}
    <div class="panel-heading">
      <h3 class="panel-title"><i class="fa fa-images m-r-5"></i>{{ trans('listings.form.image_upload.images') }}</h3>
    </div>

    <div class="panel-body">
      <div class="dropzone hidden">
        <div class="images-empty-messages-holder dz-default dz-message">
          <i class="fa fa-cloud-upload" aria-hidden="true"></i><br>{{ trans('listings.form.image_upload.empty_message') }}
        </div>
        <div class="add-image dz-clickable">
          <div>
            <span class="fa fa-plus"></span>
          </div>
        </div>
      </div>

    </div>

  </section>
  {{-- End Images Panel --}}
@endif

  {{-- Start Sell / Trade selection --}}
  <div class="selection">
    {{-- Pattern --}}
    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
    {{-- Color Gradient --}}
    <div class="background-color"></div>
    {{-- Sell Trigger --}}
    <a href="javascript:void(0)" class="trigger sell {{ !isset($listing) || (isset($listing) && $listing->sell) ? '' : 'disabled' }}" id="trigger-sell">
      <span class="text">
        <span class="icon fa-stack">
          <i class="fa fa-shopping-basket fa-stack-1x"></i>
          <i class="fa fa-ban fa-stack-2x ban" id="sell-ban" style="opacity: {{ !isset($listing) || isset($listing) && $listing->sell ? '0' : '1' }};"></i>
        </span>
        {{ trans('listings.general.sell') }}
      </span>
    </a>
    {{-- Trade Trigger --}}
    <a href="javascript:void(0)" class="trigger trade {{ isset($listing) && $listing->trade ? '' : 'disabled' }}" id="trigger-trade">
      <span class="text">
        <span class="fa-stack icon">
          <i class="fa fa-exchange fa-stack-1x"></i>
          <i class="fa fa-ban fa-stack-2x ban" id="trade-ban" style="opacity: {{ (isset($listing) && $listing->trade) ? '0' : '1' }};"></i>
        </span>
        {{ trans('listings.general.trade') }}
      </span>
    </a>
  </div>
  {{-- End Sell / Trade selection --}}


  {{-- Start Sell Panel --}}
  <section class="panel {{ !isset($listing) || isset($listing) && $listing->sell ? '' : 'hidden' }}" id="sell-panel">
    {{-- Sell Panel Header --}}
    <div class="panel-heading sell">
      {{-- Background pattern --}}
      <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
      <div class="bg-color"></div>
      <div class="panel-heading-listing-form-content flex-center-space">
        <div>
          {{-- Panel Title --}}
          <h3 class="panel-title"><i class="fa fa-shopping-basket"></i><span class="hidden-xs-down"> {{ trans('listings.form.sell_title') }}</span></h3>
        </div>
        <div class="flex-center">
          {{-- Suggestion switch --}}
          <div class="suggestion-text m-r-10">{{ trans('listings.form.sell.price_suggestions') }}</div>
          <label class="suggestion-switch m-r-10">
            <input id="sell_negotiate" name="sell_negotiate" type="checkbox" {{ ( (isset($listing) && $listing->sell_negotiate) ? 'checked' : '') }}>
            <div class="slider round"></div>
          </label>
        </div>
      </div>
    </div>

    <div class="panel-body">
      {{-- Error dialog for price--}}
      <div id="price-error-dialog"></div>

      <label>
        {{ trans('listings.form.sell.price') }} <strong><span class="text-danger">*</span></strong>
      </label>
      {{-- Input group for price --}}
      <div class="input-group input-group-lg">
        <span class="input-group-addon">
          <span>{{ Currency(Config::get('settings.currency'))->getSymbol() }}</span>
        </span>
        {{-- Price Input --}}
        <input type="text" class="form-control rounded input-lg inline input"
        data-validation="number,required" data-validation-ignore=",,." data-validation-error-msg='<div class="alert dark alert-icon alert-danger" role="alert"><i class="icon fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans('listings.form.validation.price') }}</div>' data-validation-error-msg-container="#price-error-dialog" name="price" id="price" autocomplete="off" value="{{(isset($listing) ? ($listing->price > 0 ? $listing->getPrice(false) : null) : null)}}" placeholder="{{ trans('listings.form.placeholder.sell_price',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}"/>
        {{-- Status value for sell --}}
        <input class="form-control" name="sell_status" id="sell_status" type="hidden" value="{{ !isset($listing) || isset($listing) && $listing->sell ? '1' : '0' }}" />
      </div>
      <div class="m-t-5" id="avgprice">
        @if(isset($game) && $game->getAveragePrice())
        <i class="fa fa-chart-line" aria-hidden="true"></i> {!! trans('listings.form.sell.avgprice', ['game_name' => $game->name, 'avgprice' => $game->getAveragePrice() ]) !!}</strong></span>
        @endif
      </div>

      {{-- Start Payment system --}}
      @if(config('settings.payment'))
      <div class="payment-system-form {{ ( (isset($listing) && $listing->payment) || config('settings.payment_force') ? 'show-fees' : '') }} {{ ( isset($listing) && !($listing->payment || $listing->delivery) ? 'grayscale' : '') }} m-t-20">
        <div class="flex-center-space">
          {{-- Payment System head --}}
          <div class="payment-head m-l-10" >
            <i class="fa fa-shield-check"></i> {{ trans('payment.secure_payment') }}
          </div>
          {{-- Payment System toggle --}}
          <div>
            {{-- Toggle for the payment system --}}
            <label class="suggestion-switch m-r-10 {{ ( isset($listing) && !($listing->payment || $listing->delivery) ? 'hidden' : '') }}" id="enable-payment-system">
              @if(!config('settings.payment_force'))
              {{-- Check if user is force to use the payment system --}}
              <input id="enable_payment" name="enable_payment" type="checkbox" {{ ( (isset($listing) && $listing->payment) ? 'checked' : '') }}>
              <div class="slider round"></div>
              @endif
            </label>
            {{-- Delivery info --}}
            <div class="delivery-info {{ ( isset($listing) && !($listing->payment || $listing->delivery) ? '' : 'hidden') }}">
              {{ trans('payment.form.delivery_info') }}
            </div>
          </div>
        </div>
        {{-- Paymen fees --}}
        <div class="payment-fees flex-center-space">
          <div>
            {{-- "You'll get" text --}}
            <span class="info-text">{{ trans('payment.form.youll_get') }}</span>
            {{-- Amount the user will get --}}
            <span class="total">{{ Currency(config('settings.currency'))->getSymbol() }} <span id="total-amount">0,00</span></span>
            {{-- Payment fees --}}
            <span class="info-text total-fees">{{ trans('payment.form.fees') }} {{ Currency(config('settings.currency'))->getSymbol() }} <span id="total-fees">0,00</span></span>
          </div>
          {{-- Advantages of the payment system --}}
          <div class="advantages">
            {{-- Secure --}}
            {{ trans('payment.form.secure') }} <i class="fa fa-shield-check fa-lg text-success"></i><br />
            {{-- Fast --}}
            {{ trans('payment.form.fast') }} <i class="fa fa-rocket fa-lg text-success"></i><br />
            {{-- Easy --}}
            {{ trans('payment.form.easy') }} <i class="fa fa-child fa-lg text-success"></i><br />
          </div>
        </div>
      </div>
      @endif
      {{-- End Payment system --}}

    </div>

  </section>
  {{-- End Sell Panel --}}


  {{-- Start Trade Panel --}}
  <div class="{{ isset($listing) && $listing->trade ? '' : 'hidden' }}" id="trade-panel">
    {{-- Start Trade Search Panel --}}
    <section class="panel">

      <div class="panel-heading trade">
        {{-- Background pattern --}}
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        <div class="bg-color"></div>
        <div class="panel-heading-listing-form-content flex-center-space">
          <div>
            {{-- Panel Title --}}
            <h3 class="panel-title"><i class="fa fa-exchange"></i><span class="hidden-xs-down"> {{ trans('listings.form.trade_title') }}</span></h3>
          </div>
          <div class="flex-center">
            {{-- Suggestion switch --}}
            <div class="suggestion-text m-r-10">{{ trans('listings.form.trade.trade_suggestions') }}</div>
            <label class="suggestion-switch m-r-10">
              <input id="trade_negotiate" name="trade_negotiate" type="checkbox" {{ (( isset($listing) && $listing->trade_negotiate ) ? 'checked' : '') }}>
              <div class="slider round"></div>
            </label>
          </div>
        </div>
      </div>

      <div class="panel-body">
        {{-- Error dialog for empty trade list--}}
        <div id="trade-error-dialog"></div>
        <label>{{ trans('listings.form.trade.add_to_tradelist') }}</label>
        {{-- Input Group for Trade Search --}}
        <div class="input-group input-group-lg trade-game">
          <span class="input-group-addon">
            <span id="tradesearchcomplete">
              <i class="fa fa-search"></i>
            </span>
            <span class="hidden" id="tradesearching">
              <i class="fa fa-sync fa-spin"></i>
            </span>
          </span>
          {{-- Search value input --}}
          <input type="text" class="form-control rounded input-lg inline input" id="tradesearch" data-validation="trade_list_check" data-validation-error-msg-container="#trade-error-dialog">
          {{-- Status value for trade --}}
          <input name="trade_status" id="trade_status" type="hidden" value="{{ isset($listing) && $listing->trade ? '1' : '0' }}" />
        </div>
      </div>

    </section>
    {{-- End Trade Search Panel --}}

    {{-- Start Trade List --}}
    <div class="trade_list">
      @if(isset($listing) && $trade_list)
        @php $add_charge = json_decode($listing->trade_list,true); @endphp
        @foreach ($trade_list as $tgame)
          {{-- Start Trade game --}}
          <div class="tgame" id="{{$tgame->id}}">
            <div class="flex-center">
              {{-- Remove button--}}
              <div class="tremove">
                <span><a href="javascript:void(0)" class="remove_game" data-toggle="tooltip" data-placement="right" title="{{ trans('listings.form.trade.remove') }}"><i class="fa fa-trash fa-fw m-r-xs m-t-sm"></i></a></span>
              </div>
              {{-- Game cover --}}
              <div>
                <span class="avatar m-r-10"><img src="{{ $tgame->image_square_tiny}}"></span>
              </div>
              {{-- Game title & platform --}}
              <div>
                <span class="title">{{$tgame->name}}</span>
                <span class="platform-label" style="background-color:{{$tgame->platform->color}}; ">{{$tgame->platform->name}}</span>
              </div>
            </div>

            {{-- Additional charge input --}}
            <div>
              <span class="form-inline m-r-10">
                {{-- Additional charge price --}}
                <input type="text" name="trade_list[{{$tgame->id}}][price]" value="{{ money($add_charge[$tgame->id]['price'] , Config::get('settings.currency'))->format(false, Config::get('settings.decimal_place')) }}" class="get_price form-control round  input" placeholder="{{ trans('listings.form.placeholder.additional_charge',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}" style="{{ $add_charge[$tgame->id]['price_type'] == 'none' ? 'display: none;' : ''  }}">
                {{-- Additional charge type --}}
                <input type="hidden" name="trade_list[{{$tgame->id}}][price_type]" value="{{ $add_charge[$tgame->id]['price_type'] }}" class="price_type form-control">
                {{-- ( + ) button --}}
                <a href="#" class="show_getprice {{ $add_charge[$tgame->id]['price_type'] == 'want' ? 'text-success' : ''  }}" data-toggle="tooltip" data-placement="top" title="{{ trans('listings.form.trade.additional_charge_partner') }}"><i class="fa fa-plus fa-fw m-r-xs m-l-xs m-t-sm"></i></a>
                {{-- ( - ) button --}}
                <a href="#" class="show_putprice {{ $add_charge[$tgame->id]['price_type'] == 'give' ? 'text-danger' : ''  }}" data-toggle="tooltip" data-placement="top" title="{{ trans('listings.form.trade.additional_charge_self') }}"><i class="fa fa-minus fa-fw"></i></a>
              </span>
            </div>
            {{-- hidden help values --}}
            <input type="hidden" name="trade_list[{{$tgame->id}}][id]" value="{{$tgame->id}}" />
            <input type="hidden" name="trade_list[{{$tgame->id}}][name]" value="{{$tgame->name}}" />
          </div>
          {{-- End Trade game --}}
        @endforeach

      @endif
    </div>
    {{-- End Trade List --}}

  </div>
  {{-- End Trade Panel --}}

  <div class="listing-buttons" id="submit_button">
    @if(isset($listing))
      {{-- Cancel button --}}
      <a class="btn btn-lg btn-dark m-r-5" href="{{ $listing->url_slug }}"><i class="fas fa-ban"></i> {{ trans('general.cancel') }}</a>
      {{-- Save Button --}}
      <button class="btn btn-lg btn-success" type="submit" id="submit-button"><i class="fa fa-save"></i> {{ trans('listings.form.save_button') }}</button>
    @else
      {{-- Add Button --}}
      <button class="btn btn-lg btn-success" type="submit" id="submit-button"><i class="fa fa-plus"></i> {{ trans('listings.form.add_button') }}</button>
    @endif
  </div>

</div>
{!! Form::close() !!}
{{-- End Listing Form --}}


{{-- Check if user can add games to the system --}}
@if(Config::get('settings.user_add_item'))
{{-- Start Modal for adding new game to database --}}
<div class="modal fade modal-fade-in-scale-up modal-success" id="TradeGameAdd" tabindex="-1" role="dialog">
  <div class="modal-dialog user-dialog">
    <div class="modal-content">

      <div class="modal-header">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        <div class="background-color"></div>

        <div class="title">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('listings.modal.close') }}</span>
          </button>
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-plus m-r-5" aria-hidden="true"></i>
            <strong>{{ trans('listings.modal_game.title') }}</strong>
          </h4>
        </div>

      </div>

    {{-- Open form for search --}}
    <form id="searchForm" method="POST" novalidate="novalidate">

      <div class="modal-body" style="background-color: #191818;">
        <div class="loading text-center modal-loading">
          <div class="loader-item"><div class="loader pacman-loader lg"></div></div>
          <span>
              <strong>{{ trans('listings.modal_game.adding',  ['pagename' =>Config::get('settings.page_name')]) }}</strong> <br> <span id="please_wait">{{ trans('listings.modal_game.wait') }}</span>
          </span>
        </div>

        <!-- Start  Main Content -->
        <div class="main-content">
          <?php
          $platforms = DB::table('platforms')->get();
          ?>

          <!-- Start search bar for searching games -->
          <div class="input-group input-group-lg" id="search_bar">
            <div class="input-group-btn search-panel">
              <button type="button" id="platform_select" class="btn dropdown-toggle dropdown-system dropup" data-toggle="dropdown">
                  <span id="search_concept">{{ trans('listings.modal_game.select_system') }}</span> <span class="caret"></span>
              </button>
              <ul class="dropdown-menu systems" role="menu">
                @foreach($platforms as $platform)

                <li><a href="#{{ $platform->acronym }}" data-color="{{$platform->color}}">{{ $platform->name }}</a></li>
                  @if($loop->iteration == 7)
                    <li class="divider" role="presentation"></li>
                    <li class="dropdown-submenu">
                      <a href="javascript:void(0)" tabindex="-1">{{ trans('listings.modal_game.more') }} <i class="fa fa-caret-right" aria-hidden="true" style="float: right;"></i></a>
                      <ul class="dropdown-menu systems" role="menu" style="top: -300px !important;">
                  @endif
                  @if($loop->iteration == count($platforms))
                      </ul>
                    </li>
                  @endif
                @endforeach
              </ul>
            </div>
            <input type="hidden" name="search_param" value="all" id="search_param">
            <input type="hidden" name="trade_search" value="1" id="search_param">
            <input type="text" id="appendedInput" name="game" class="form-control input" placeholder="{{ trans('listings.modal_game.placeholder.value') }}">
          </div>
          <!-- End search bar for searching games -->

          {{-- Loading bar --}}
          <div class="loading-bar hidden" id="loading_bar">
            <i class="fa fa-spinner fa-pulse fa-fw"></i> {{ trans('listings.modal_game.searching') }}
          </div>

          <div id="searchresult">
          </div>

        </div>
        <!-- End Main Content -->
      </div>

      <div class="modal-footer" id="search_footer">
        <a href="javascript:void(0)" data-dismiss="modal" data-bjax class="btn btn-default btn-lg">{{ trans('listings.modal.close') }}</a>
        <button class="send-search btn btn-success btn-animate btn-animate-vertical btn-lg" type="submit">
          <span><i class="icon fa fa-search" aria-hidden="true"></i> {{ trans('listings.modal_game.search') }}
          </span>
        </button>
        <button class="error-search btn btn-light btn-lg" type="button" disabled>
          {{ trans('listings.modal_game.select_system') }}
        </button>
      </div>

    </form>
    {{-- Close form for search --}}

    </div>
  </div>
</div>
{{-- End Modal for adding new game to database --}}
@endif

{{-- Loading overlay --}}
<div class="loading-backdrop hidden">
  <div class="loading-wrapper">
    <i class="fas fa-spinner fa-pulse"></i>
  </div>{{--loading-wrapper--}}
</div>{{--loading-backdrop--}}

@stop




@section('after-scripts')

{{-- Check if location is saved, otherwise open modal --}}
@if(!Auth::user()->location)
  @include('default::frontend.user.location.' . config('settings.location_api') )
@endif



<link href="{{ asset('vendor/summernote/summernote_frontend.css') }}" rel="stylesheet" type="text/css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/mustache.js/2.3.0/mustache.min.js"></script>
<script src="{{ asset('js/autoNumeric.min.js') }}"></script>
<script src="{{asset('vendor/summernote/summernote.js')}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

{{-- Load DropZone JS and CSS files --}}
@if(config('settings.picture_upload') || (isset($listing) && !is_null($listing->picture)))
  <script src="{{ asset('vendor/dropzone/dropzone.min.js') }}"></script>
  <link href="{{ asset('vendor/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
  <script src="{{ asset('vendor/dropzone/Sortable.min.js') }}"></script>
  <script src="{{ asset('vendor/dropzone/jquery.binding.js') }}"></script>
@endif


{{-- Start Mustache Template for selected game --}}
<script id="selected-game" type="x-tmpl-mustache">

  <section class="panel" id="game-<% id %>">

    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('listings.form.game.selected') }}</h3>
    </div>

    <div class="panel-body" >
      <div class="flex-center">
        <div>
          <span class="avatar m-r-10"><img src="<% pic %>" /></span>
        </div>
        <div>
            <span class="selected-game-title">
              <% name %><span class="release-year m-l-5"><% release_year %></span>
            </span>
            <span class="platform-label" style="background-color:<% platform_color %>;">
              <% platform_name %>
            </span>
        </div>
      </div>
    </div>

    <div class="panel-footer game-add">
      <div><a href="javascript:void(0)" class="reselect-game add-link m-r-10"><i class="fa fa-repeat" aria-hidden="true"></i> {{ trans('listings.form.game.reselect') }}</a><span class="text">{{ trans('listings.form.game.reselect_info') }}</span></div>
    </div>

    <input name="game_id" type="hidden" value="<% id %>">

  </section>

</script>
{{-- End Mustache Template for selected game --}}

{{-- Start Mustache Template for trade game --}}
<script id="template" type="x-tmpl-mustache">

  <div id="<% id %>" class="tgame">
    <div class="flex-center">
      <div class="tremove">
        <span><a href="#" class="remove_game" data-toggle="tooltip" data-placement="right" title="{{ trans('listings.form.trade.remove') }}"><i class="fa fa-trash fa-fw m-r-xs m-t-sm"></i></a></span>
      </div>
      <div>
        <span class="avatar m-r-10"><img src="<% pic %>"></span>
      </div>
      <div>
          <span class="title">
            <% name %><span class="release-year m-l-5"><% release_year %></span>
          </span>
          <span class="platform-label" style="background-color:<% platform_color %>;">
            <% platform_name %>
          </span>
      </div>
    </div>

    <div>
      <span class="form-inline m-r-10">
        <input type="text" name="trade_list[<% id %>][price]" value="0" class="get_price form-control round input" placeholder="{{ trans('listings.form.placeholder.additional_charge',  ['currency_name' => Currency(Config::get('settings.currency'))->getName()]) }}" style="display:none;">
        <input type="hidden" name="trade_list[<% id %>][price_type]" value="none" class="price_type form-control">
        <a href="#" class="show_getprice" data-toggle="tooltip" data-placement="top" title="{{ trans('listings.form.trade.additional_charge_partner') }}">
          <i class="fa fa-plus fa-fw m-r-xs m-l-xs m-t-sm"></i></a>
        <a href="#" class="show_putprice" data-toggle="tooltip" data-placement="top" title="{{ trans('listings.form.trade.additional_charge_self') }}">
          <i class="fa fa-minus fa-fw"></i>
        </a>
      </span>
    </div>

    <input type="text" name="trade_list[<% id %>][id]" value="<% id %>" class="form-control" placeholder="Aufzahlung" style="display:none">
    <input type="text" name="trade_list[<% id %>][name]" value="<% name %>" class="form-control" placeholder="Aufzahlung" style="display:none">


  </div>

</script>
{{-- End Mustache Template for trade game --}}

<script type="text/javascript">
// Disable Dropzone AutoDiscover
Dropzone.autoDiscover = false;
$(document).ready(function(){

{{-- Payment system functions --}}
@if(config('settings.payment'))

  {{-- Calculate payment fees --}}
  var payment_fees = function(price) {
    var new_price = price;
    var fees = @if(config('settings.variable_fee') || config('settings.fixed_fee')) (@if(config('settings.variable_fee')) new_price * {{ config('settings.variable_fee')/100 }} @endif @if(config('settings.fixed_fee')) + {{ config('settings.fixed_fee') }} @endif) @else 0 @endif;
    var end_price = (Math.round((new_price - fees) * 100)/100).toFixed(2);
    var end_fees = (Math.round((fees) * 100)/100).toFixed(2);
    if (end_price > 0) {
      $('#total-amount').html(end_price.replace(".", "{{ Currency(config('settings.currency'))->getDecimalMark() }}"));
    }else{
      $('#total-amount').html('0.00'.replace(".", "{{ Currency(config('settings.currency'))->getDecimalMark() }}"));
    }

    $('#total-fees').html(end_fees.replace(".", "{{ Currency(config('settings.currency'))->getDecimalMark() }}"));
    return end_price;
  };

  @if(!config('settings.payment_force'))
  {{-- Toggle payment system --}}
  $('#enable_payment').click(function(e){
    $('.payment-system-form').toggleClass('show-fees');
  });
  @endif

  {{-- Change payment fees on price input --}}
  $("#price").on("change keyup paste click", function(){
    payment_fees($('#price').autoNumeric('getNumber'));
  });

@endif

{{-- Check if image upload is enabled in the admin panel --}}
@if(config('settings.picture_upload') || (isset($listing) && !is_null($listing->picture)))

  {{-- Image queue --}}
  var current_queue = [];
  {{-- Dropzone container --}}
  var imageDrop = $(".dropzone");
  {{-- Add more images container --}}
  var addMoreImages = $(".add-image");

  {{-- DropZone Options --}}
  imageDrop.dropzone({
    url: '{{ url(isset($listing) ? 'listings/' .  $listing->id . '/images/upload' : 'listings/images/upload')}}',
    clickable: '.add-image, .dropzone',
    maxFiles: 4,
    parallelUploads: 1,
    acceptedFiles: 'image/*',
    addRemoveLinks: true,
    maxFilesize: 3,
    @if(!isset($listing))
    uploadMultiple: false,
    @endif
    dictRemoveFile: '<i class="fa fa-trash"></i>',
    dictMaxFilesExceeded: '{{ trans('listings.form.image_upload.max_files_exceeded') }}',
    dictInvalidFileType: '{{ trans('listings.form.image_upload.invalid_type') }}',
    @if(isset($listing))
    autoProcessQueue: true,
    @else
    autoProcessQueue: false,
    @endif
    sending: function(file, xhr, formData) {
      formData.append("_token", Laravel.csrfToken);
    },
    complete: function(file, response) {
      if (file._removeLink) {
        file._removeLink.innerHTML = this.options.dictRemoveFile;
      }
      if (file.previewElement) {
        return file.previewElement.classList.add("dz-complete");
      }
    },
    success: function(file, resp) {
      if (file.upload) {
        this.removeFile(file);
        var file = {
            name: resp.filename,
            size: '0',
            status: 'added',
            accepted: true,
            order: resp.order
        };
        this.emit('addedfile', file);
        this.emit('success', file,);
        this.emit('thumbnail', file, resp.thumbnail);
        this.emit('complete', file, true);
        this.files.push(file);
      }

    },
    init: function() {
      imageDrop.removeClass("hidden")
      this.on("addedfile", function(file) {

          imageDrop.children().insertBefore(addMoreImages);

          if(this.files.length >= this.options.maxFiles) {
              addMoreImages.addClass("hidden");
          }
      });
      this.on("removedfile", function(file) {
          if(this.files.length < this.options.maxFiles) {
              addMoreImages.removeClass("hidden");
          }
      });
      @if(isset($listing))
      $.getJSON('{{ url('listings/' . $listing->id . '/images') }}', function (data) {
          $.each(data, function (key, value) {
              add_image_dz(value);
          });
      });
      @endif

    }
  });

  {{-- Dropzone var --}}
  var myDropzone = Dropzone.forElement(".dropzone");

  @if(isset($listing))
    var add_image_dz = function (resp) {
      var file = {
          name: resp.filename,
          size: '0',
          status: 'added',
          accepted: true,
          order: resp.order
      };
      myDropzone.emit('addedfile', file);
      myDropzone.emit('success', file,);
      myDropzone.emit('thumbnail', file, resp.thumbnail);
      myDropzone.emit('complete', file, true);
      myDropzone.files.push(file);
    };
  @endif


  @if(!isset($listing))
    // Initialize Submit Button
    var submitButton = $('#submit-button');
    var uploading_files = false;
    var listing_url;
    var listing_id;

    // Submit Button Event on click
    $('#form-listing').on('submit', function(e) {
      e.preventDefault();
      {{-- Check if form is valid --}}
      if ($(this).isValid()) {
        var loadingBackdrop = $(".loading-backdrop");
        // Serialize Form
        var form = $('#form-listing').serializeArray();
        var html = $('#description').code();
        form.push({name:'description', value:html});
        $.ajax({
            type: 'POST',
            url: $('#form-listing').attr('action'),
            data: form,
            beforeSend: function() {
              loadingBackdrop.removeClass("hidden");
            },
            success: function(data) {
              console.log(data);
              listing_url = data.url_slug;
              listing_id = data.id;
              uploading_files = true;
              if (myDropzone.getQueuedFiles().length > 0) {
                myDropzone.processQueue();
              } else {
                window.location={{ isset($listing) ? '"' . $listing->url . '"' : 'listing_url' }};
              }
            },
            error: function(data) {
              $("html, body").animate({ scrollTop: 0 }, "slow");
              $.each(data.responseJSON.errors, function(key, value ) {
                $('#' + key).addClass('is-invalid');
                $('#' + key + '-error').html(value);
              });
              loadingBackdrop.addClass("hidden");
            }
        });
      }
    });
  @else
  var uploading_files = false;
  var listing_id = {{$listing->id}};
  @endif

  @if(isset($listing))
    var sort = function (curr_file_name = null, just_sort = false) {
      var sorting_queue = new Array();
      $.each(myDropzone.files, function (index, file) {
          if (curr_file_name !== null && file.name == curr_file_name) {
              return true; // skip to next
          }
          sorting_queue.push(file.name);
      });
      $.ajax({
          url: '{{ url('listings/' .  $listing->id . '/images/sort')}}',
          type: 'POST',
          dataType: 'json',
          data: {
            _token: Laravel.csrfToken,
            order: JSON.stringify(sorting_queue)
          },
          success: function (data) {

          }
      });
    };
  @endif

  var move_last_to_pos = function (order) {
    // move newly added image to proper position
    var selector = $('.dropzone .dz-preview');
    var new_image = selector.last();
    var total = selector.length;
    // each starts at 0, count starts at 1, add 1 to count
    selector.each(function (count, el) {
        if (count + 1 === order) {
            // if element isn't the same as the new image
            // if element isn't the last
            if (el !== new_image && order !== total) {
                jQuery(new_image).detach().insertBefore(el);
            }
            return false; // break
        }
    });
  };



  myDropzone.on('addedfile', function (file, start) {
    if ($.inArray(file.name, current_queue) !== -1) {
        current_queue.push(file.name);
        notie.alert('error', '{{ trans('listings.form.image_upload.already_exists') }}',5);
        //errors.html('');
        myDropzone.removeFile(file);
    } else {
        // order is already added for existing images onload
        if (!start) {
            // add order as last by default
            file.order = current_queue.length;
        }
        current_queue.push(file.name);
    }
  });

  myDropzone.on('removedfile', function (file) {
    current_queue.splice($.inArray(file.name, current_queue), 1);
    @if(isset($listing))
    sort();
    $.ajax({
        url: '{{ url('listings/' .  $listing->id . '/images/remove')}}',
        type: 'POST',
        dataType: 'json',
        data: {
            _token: Laravel.csrfToken,
            filename: file.name,
            order: file.order
        },
        success: function (data) {
        }
    });
    @endif
  });

  // on sending via dropzone append token and form values (using serializeObject jquery Plugin)
  myDropzone.on("sending", function(file, xhr, formData) {
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('listing_id', listing_id);
    formData.append('order', file.order);
  });

  myDropzone.on("success", function(file) {
    @if(isset($listing))
      sort();
    @endif
    myDropzone.options.autoProcessQueue = true;
    @if(!isset($listing))
      if (myDropzone.getQueuedFiles().length == 0) {
        window.location=listing_url;
      }
    @endif
  });


  myDropzone.on("queuecomplete", function(){
    if (uploading_files) {
      @if(isset($listing))
        window.location="{{ $listing->url_slug }}";
      @else
        window.location=listing_url;
      @endif
    }
  });


  // on error show errors
  myDropzone.on("error", function(file, errorMessage, xhr){
    notie.alert('error', errorMessage,5);
  });


  {{-- Make Dropzone images sortable --}}
  imageDrop.sortable({
    filter: '.add-image',
    draggable: ".dz-preview",
    cursor: 'move',
    forceFallback: true,
    tolerance: 'pointer',
    onStart: function (evt) {
      addMoreImages.hide();
    },
    onEnd: function (evt) {
      addMoreImages.show();
      var queue = myDropzone.files;
      var new_queue = [];
      $('.dropzone .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
          var name = el.innerHTML;
          queue.forEach(function (file) {
              if (file.name === name) {
                  file.order = count + 1;
                  new_queue.push(file);
              }
          });
      });
      myDropzone.files = new_queue;
      @if(isset($listing))
        sort();
      @endif
    },
    onMove: function (event) {
      return addMoreImages !== event.related;
    }
  });

@endif

  {{-- Start Game Add JS --}}

  // Loading text animation for modal
  var originalText = $("#please_wait").text(),
      i  = 0;
  setInterval(function() {

      $("#please_wait").append(".");
      i++;

      if(i == 4)
      {
          $("#please_wait").html(originalText);
          i = 0;
      }

  }, 500);

  $('.loading').hide();
  $('#loadingoffersearch').hide();
  $('#loading_bar').hide();
  $('.send-search').hide();

  // get platform
  var platform = "no";
  $('.search-panel .dropdown-menu').find('a').click(function(e) {
  e.preventDefault();
      platform = $(this).attr("href").replace("#","");
      color = $(this).data("color");
  var concept = $(this).text();
  $('.search-panel span#search_concept').text(concept);
  $('.input-group #search_param').val(platform);
      $('#platform_select').css("background-color", color );


      // Check if platform is selected
      if($(this).attr("href") == "no") {
          $('.send-search').fadeOut(200).promise().done(function(){
              $('.error-search').fadeIn(200);
          });
      }else{
          $('.error-search').fadeOut(200).promise().done(function(){
              $('.send-search').fadeIn(200);
          });

      }

    });

    {{-- Check if search input have value --}}
    $("#appendedInput").keyup(function(event){
      $('#appendedInput').val() == '' ? $('.send-search').attr('disabled', true) : $('.send-search').attr('disabled', false);
    });


    {{-- Start Form submit and get ajax results --}}
    $("#searchForm").submit(function(e){
      e.preventDefault();
      if(platform != "no" && $('#appendedInput').val()){
        var searchForm = $("#searchForm");
        var searchData = searchForm.serialize();

        $.ajax({
            url:'{{ url("games/api/search") }}',
            type:'POST',
            data:searchData,
            {{-- Send CSRF Token over ajax --}}
            headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
            beforeSend: function(){
              $( "#searchresult" ).fadeOut('slow');

              $('.send-search').attr('disabled', true);
              $(".send-search").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

              $('#loadingoffercomplete').hide();
              $('#loadingoffersearch').show();

              $('#search_bar').fadeOut(300).promise().done(function(){
                  $('#loading_bar').fadeIn(200);
              });

            },
            success:function(data){
              $( "#searchresult" ).hide().html(data).fadeIn('slow');


              $('#loadingoffercomplete').show();
              $('#loadingoffersearch').hide();

              $('#loading_bar').fadeOut(300).promise().done(function(){
                   $('#search_bar').fadeIn(200);
              });
              $('.send-search').attr('disabled', false);
              $(".send-search").html('<span><i class="icon fa fa-search" aria-hidden="true"></i> {{ trans('listings.modal_game.search') }}</span>');

            },
            error: function (data) {
              alert('Oops, an error occurred!')
              $('#loadingoffercomplete').show();
              $('#loadingoffersearch').hide();

              $('#loading_bar').fadeOut(300).promise().done(function(){
                   $('#search_bar').fadeIn(200);
              });
              $('.send-search').attr('disabled', false);
              $(".send-search").html('<span><i class="icon fa fa-search" aria-hidden="true"></i> {{ trans('listings.modal_game.search') }}</span>');
            }
        });
      }
    });
    {{-- End Form submit and get ajax results --}}


  {{-- End Game Add JS --}}



  {{-- Start typeahead for listing game search --}}
  {{-- Bloodhound engine with remote search data in json format --}}
  $('#offersearch').submit(false);
  var listingGameSearch = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: '{{ url("games/search/json/%QUERY") }}',
      wildcard: '%QUERY'
    }
  });

  {{-- Typeahead with data from bloodhound engine --}}
  $('#offersearch').typeahead(null, {
    name: 'offer-search',
    display: 'name',
    source: listingGameSearch,
    highlight: true,
    limit:6,
    templates: {
      {{-- Check if user can add games to the system --}}
      @if(Config::get('settings.user_add_item'))
      empty: [
        '<div class="nosearchresult bg-danger"><a href="{{ url("games/add") }}">',
          '<span><i class="fa fa-ban"></i> {{ trans('listings.form.validation.no_game_found') }} <strong>{{ trans('listings.form.validation.no_game_found_add') }}</strong><span>',
        '</a></div>'
      ].join('\n'),
      @else
      empty: [
        '<div class="nosearchresult bg-danger">',
          '<span><i class="fa fa-ban"></i> {{ trans('listings.form.validation.no_game_found') }}<span>',
        '</div>'
      ].join('\n'),
      @endif
      suggestion: function (data) {
          return '<div class="searchresult hvr-grow-shadow2"><span class="link"><div class="inline-block m-r-10"><span class="avatar"><img src="' + data.pic + '" class="img-circle"></span></div><div class="inline-block"><strong class="title">' + data.name + '</strong><span class="release-year m-l-5">' + data.release_year +'</span><br><small class="text-uc text-xs"><span class="platform-label" style="background-color: ' + data.platform_color + ';">' + data.platform_name + '</span></small></div></span></div>';
      }
    }
  })
  .on('typeahead:asyncrequest', function() {
      $('#listingsearchcomplete').hide();
      $('#listingsearching').show();
  })
  .on('typeahead:asynccancel typeahead:asyncreceive', function() {
      $('#listingsearching').hide();
      $('#listingsearchcomplete').show();
  });

  {{-- Change selected game on selecting typeahead --}}
  $('#offersearch').bind('typeahead:selected', function(obj, datum, name) {
    var customTags = [ '<%', '%>' ];
    Mustache.tags = customTags;
    var template = $('#selected-game').html();
    Mustache.parse(template);   // optional, speeds up future uses
    var append_date = Mustache.render(template, datum);
    $('#select-game').slideUp(300);avgprice
    $(append_date).hide().appendTo('.selected-game').slideDown(300);
    $('.listing-form').delay(300).slideDown(300);
    setTimeout(function(){$('#offersearch').typeahead('val', ''); }, 10);
    {{-- Show average selling price if one exists --}}
    if (datum.avgprice) {
      $('#avgprice').html('<i class="fa fa-chart-line" aria-hidden="true"></i> ' + datum.avgprice_string);
    } else {
      $('#avgprice').html('');
    }
    {{-- Hide digital input when platform dont support digital distributors --}}
    if (!datum.platform_digital) {
      $('#digital-input').addClass('hidden');
    } else {
      $('#digital-input').removeClass('hidden');
      $('#digital').data('platform', datum.platform_acronym)
    }
    {{-- Check if digital downloads only is enable --}}
    @if(config('settings.digital_downloads_only'))
    loadDigitalDistributors(datum.platform_acronym);
    @endif
  });

  {{-- Reset game --}}
  $('.selected-game').on('click', '.reselect-game', function(e) {
      e.preventDefault();
      $('.listing-form').slideUp(300);
      $(this).parent().parent().parent().delay(300).slideUp(300, function() {
          $(this).remove();
      });
      $('#select-game').delay(300).slideDown(300);
      {{-- Reset digital download --}}
      $('#digital_distributor').empty();
      {{-- Check if digital downloads only is enable --}}
      @if(!config('settings.digital_downloads_only'))
      $('#digital').prop( "checked", false );
      $('#digital-select').slideUp(300);
      @endif
  });
  {{-- End typeahead for listing game search --}}

  {{-- Function to check if html is empty --}}
  function isEmpty( el ){
      return !$.trim(el.html())
  };

  {{-- Validator for delivery and pickup - One option need to be selected --}}
  $.formUtils.addValidator({
    name : 'delivery_pickup_check',
    validatorFunction : function() {
      if($("#pickup").is(':checked') || $("#delivery").is(':checked')) {
        $("#pickup").removeClass('error');
        $("#delivery").removeClass('error');
        return true;
      }
      else{
        $("#pickup").addClass('error');
        $("#delivery").addClass('error');
        return false;
      }
    },
    errorMessage : '<div class="alert dark alert-icon alert-danger" role="alert"><i class="icon fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans('listings.form.validation.delivery_pickup') }}</div>',
    errorMessageKey: 'wrongDeliveryPickup'
  });

  {{-- Validator for tradelist - at least one game need to be in the list --}}
  $.formUtils.addValidator({
    name : 'trade_list_check',
    validatorFunction : function() {
      if (isEmpty($('.trade_list'))) {
        {{-- Check if user accept trade suggestions --}}
        if ($('#trade_negotiate').is(":checked")) {
          return true;
        }else{
          return false;
        }
      }else{
          return true;
      }
    },
    errorMessage : '<div class="alert dark alert-icon alert-danger" role="alert"><i class="icon fa fa-exclamation-triangle" aria-hidden="true"></i> {{ trans('listings.form.validation.trade_list') }}</div>',
    errorMessageKey: 'emptyTradeList'
  });

  {{-- Form validator --}}
  $.validate({
    form: '#form-listing',
    borderColorOnError : '#00',
    addValidClassOnAll : true,
    validateOnBlur : true,
    scrollToTopOnError : false,
    validateOnEvent : true,
    onSuccess : function($form) {
      $('#submit-button').attr('disabled', 'disabled');
      $('#submit-button').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $(".loading-backdrop").removeClass('hidden');
      $('#zustand').removeAttr('disabled');
    },
    onError : function($form) {
      $('#submit-button').shake({
        speed: 80
      });
    }
  });

  {{-- Turn description textarea to summernote --}}
  $('#description').summernote({
    toolbar: [
      // [groupName, [list of button]]
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['font', ['strikethrough']],
      ['para', ['ul', 'ol']]
    ],
    height: '150',
    disableDragAndDrop: true
  });



  {{-- Start mask prices for money input --}}
  const autoNumericOptions = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
  };

  {{-- Start mask prices for money input with currency symbol --}}
  const autoNumericOptionsSymbol = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
      currencySymbol             : '{{ Currency(Config::get('settings.currency'))->getSymbol() }}{{ Currency(Config::get('settings.currency'))->isSymbolFirst() ? ' ' : '' }}',
      currencySymbolPlacement    : '{{ Currency(Config::get('settings.currency'))->isSymbolFirst() ? 'p' : 's' }}',
  };

  // Initialization
  $('#price').autoNumeric('init', autoNumericOptions);

  @if(isset($listing) && $listing->sell && $listing->price > 0)
    {{-- Calculate payment fees --}}
    @if(config('settings.payment'))
    payment_fees($('#price').autoNumeric('getNumber'));
    @endif
  @endif

  {{-- Price Validation fix --}}
  $("#price").focusout(function() {
    if($("#price").val() == '0{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}00') {
      $("#price").val('');
    }
    $("#price").validate();
  });

  $("#delivery_price").autoNumeric('init', autoNumericOptions);

  $('.get_price').autoNumeric('init', autoNumericOptionsSymbol);

  {{-- End mask prices for money input --}}

  {{-- Start fade in delivery cost on checked checkbock --}}
  var delivery_info = $('.delivery-info');
  var enable_payment = $('#enable-payment-system');
  $('#delivery').click(function() {
      if( $(this).is(':checked')) {
          $("#delivery_cost").slideDown('fast');
          delivery_info.fadeOut(200,function(){
              enable_payment.fadeIn(200)
          });

          $('.payment-system-form').removeClass('grayscale');

      } else {
          $("#delivery_cost").slideUp('fast');
          enable_payment.fadeOut(200,function(){
            delivery_info.fadeIn(200);
          });
          $('.payment-system-form').addClass('grayscale');

      }
  });
  {{-- End fade in delivery cost on checked checkbock --}}

  {{-- Start fade in limited edition on checked checkbock --}}
  $('#limited').click(function() {
      if( $(this).is(':checked')) {
          $("#limited_form").slideDown('fast');
      } else {
          $("#limited_form").slideUp('fast');
      }
  });
  {{-- End fade in limited edition on checked checkbock --}}

  {{-- Start function to load digital distrubutors --}}
  function loadDigitalDistributors($platform) {
      if( $.trim($("#digital_distributor").html())=='' ) {
        $.ajax({
          url: '{{ url("api/digitals") }}/' + $platform,
          dataType: "json",
          type: "GET",
          beforeSend: function(){
            $('#digital_distributor').empty().append('<option selected="selected" value="whatever">Get digitals distributors...</option>').attr('disabled');
          },
          success: function(data) {
            $('#digital_distributor').removeAttr('disabled');
            $('#digital_distributor').empty();
            $.each(data, function(i, value) {
              if(i == 0){
                $('#digital_distributor').append($('<option>').text(value['name']).attr('value', value['id']).attr('selected', 'selected'));
              }else{
                $('#digital_distributor').append($('<option>').text(value['name']).attr('value', value['id']));
              }
            });
          },
          error: function (data) {

          }
        });
      }
  }
  {{-- End function to load digital distrubutors --}}

  @if(isset($game))
  loadDigitalDistributors('{{ $game->platform->acronym }}');
  @endif

  {{-- Start fade in digital selection on checked checkbock --}}
  $('#digital').click(function() {

    loadDigitalDistributors($(this).data('platform'));

    if( $(this).is(':checked')) {
      $('#condition').append($('<option>', {
          value: 0,
          text: '{{ trans('listings.general.conditions.0') }}'
      }));
      $('#condition').attr('disabled', 'disabled');
      $('#condition').val("0");
      $("#digital-select").slideDown('fast');
    } else {
      $("#condition option[value='0']").remove();
      $('#condition').val("5");
      $('#condition').removeAttr('disabled');
      $("#digital-select").slideUp('fast');
    }
  });
  {{-- End fade in digital selection on checked checkbock --}}




  {{-- Trigger for sell --}}
  $("#trigger-sell").click( function(e){
    e.preventDefault();

    if( $("#sell_status").val() == 1){

      if( $("#trade_status").val() == 0){
        $("#submit_button").slideUp("fast");
      }

      $("#sell-panel").slideUp("fast");
      $("#sell_status").val("0");
      $("#sell-ban").css('opacity','1');
      $(this).toggleClass( "disabled", true );
      $('input[name=price]').val("");

    }else{
      $("#sell-panel").slideDown("fast");
      $("#sell_status").val("1");
      $("#sell-ban").css('opacity','0');
      $("#submit_button").slideDown("fast");
      $(this).toggleClass( "disabled", false );
    }



  });

  {{-- Trigger for trade --}}
  $("#trigger-trade").click( function(e){
    e.preventDefault();

    if( $("#trade_status").val() == 1){

      if( $("#sell_status").val() == 0){
          $("#submit_button").slideUp("fast");
      }

      $("#trade-panel").slideUp("fast");
      $("#trade_status").val("0");
      $("#trade-ban").css('opacity','1');
      $(this).toggleClass( "disabled", true );

    }else{
      $("#trade-panel").slideDown("fast");
      $("#trade_status").val("1");
      $("#trade-ban").css('opacity','0');
      $("#submit_button").slideDown("fast");
      $(this).toggleClass( "disabled", false );
    }
  });

  {{-- Enable tooltips for tradelist --}}
  @if(isset($listing))
    $('[data-toggle="tooltip"]').tooltip();
  @endif

  {{-- Start typeahead for trade game search --}}
  $('#tradesearch').submit(false);
  var tradeSearchData = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
      url: '{{ url('games/search/json/%QUERY') }}',
      wildcard: '%QUERY'
    }
  });


  $('#tradesearch').typeahead(null, {
    name: 'trade-search',
    display: 'name',
    source: tradeSearchData,
    highlight: true,
    limit:6,
    templates: {
      {{-- Check if user can add games to the system --}}
      @if(Config::get('settings.user_add_item'))
      empty: [
        '<div class="nosearchresult bg-danger"><a href="javascript:void(0)" data-toggle="modal" data-target="#TradeGameAdd">',
          '<span><i class="fa fa-ban"></i> {{ trans('listings.form.validation.no_game_found') }} <strong>{{ trans('listings.form.validation.no_game_found_add') }}</strong><span>',
        '</a></div>'
      ].join('\n'),
      @else
      empty: [
        '<div class="nosearchresult bg-danger">',
          '<span><i class="fa fa-ban"></i> {{ trans('listings.form.validation.no_game_found') }}<span>',
        '</div>'
      ].join('\n'),
      @endif
      suggestion: function (data) {
          var price;
          return '<div class="searchresult hvr-grow-shadow2"><span class="link"><div class="inline-block m-r-10"><span class="avatar"><img src="' + data.pic + '" class="img-circle"></span></div><div class="inline-block"><strong class="title">' + data.name + '</strong><span class="release-year m-l-5">' + data.release_year +'</span><br><small class="text-uc text-xs"><span class="platform-label" style="background-color: ' + data.platform_color + ';">' + data.platform_name + '</span></small></div></span></div>';
      }
    }
  })
  .on('typeahead:asyncrequest', function() {
      $('#tradesearchcomplete').hide();
      $('#tradesearching').show();
  })
  .on('typeahead:asynccancel typeahead:asyncreceive', function() {
      $('#tradesearching').hide();
      $('#tradesearchcomplete').show();
  });


  $('#tradesearch').bind('typeahead:selected', function(obj, datum, name) {
    var customTags = [ '<%', '%>' ];
    Mustache.tags = customTags;
    var template = $('#template').html();
    Mustache.parse(template);   // optional, speeds up future uses
    var append_date = Mustache.render(template, datum);
    $(append_date).hide().appendTo('.trade_list').slideDown("fast", function() {
      $('#tradesearch').validate();
    });
    {{-- Enable tooltip on new game element --}}
    $('[data-toggle="tooltip"]').tooltip();
    {{-- Enable maskMoney on new game element --}}
    $('.get_price').autoNumeric('init', autoNumericOptionsSymbol);
    setTimeout(function(){$('#tradesearch').typeahead('val', ''); }, 10);
  });

  {{-- Remove game from tradelist when remove button is clicked --}}
  $('.trade_list').on('click', '.remove_game', function(e) {
    e.preventDefault();
    $(this).tooltip('hide')
    $(this).parent().parent().parent().parent().slideUp("fast", function() {
        $(this).remove();
        $('#tradesearch').validate();
    });
  });
  {{-- End typeahead for trade game search --}}

  {{-- Additional charge from user --}}
  $('.trade_list').on('click', '.show_getprice', function(e) {

      e.preventDefault();

      if($(this).parent().find('.get_price').is(':visible') &&                                  $(this).parent().find('.price_type').val() == "want") {
          $(this).parent().find('.get_price').hide("fast");
          $(this).parent().find('.price_type').val("none");
          $(this).removeClass("text-success");
          $(this).parent().find('.get_price').val("").focus().blur();

      }else{

          $(this).parent().find('.get_price').val("").focus().blur();
          $(this).parent().find('.get_price').show("fast");
          $(this).parent().parent().find('.price_type').val("want");
          $(this).parent().find('.show_putprice').removeClass("text-danger");
          $(this).addClass("text-success");

      }
  });

  {{-- Additional charge from partner --}}
  $('.trade_list').on('click', '.show_putprice', function(e) {
      e.preventDefault();

      if($(this).parent().find('.get_price').is(':visible') && $(this).parent().find('.price_type').val() == "give") {
          $(this).parent().find('.get_price').hide("fast");
          $(this).parent().find('.price_type').val("none");
          $(this).removeClass("text-danger");
          $(this).parent().find('.get_price').val("").focus().blur();
      }else{
          $(this).parent().find('.get_price').val("").focus().blur();
          $(this).parent().find('.get_price').show("fast");
          $(this).parent().find('.price_type').val("give");
          $(this).parent().find('.show_getprice').removeClass("text-success");
          $(this).addClass("text-danger");
      }

  });


})
</script>


<script type="text/javascript">
(function($) {
  $.fn.shake = function(o) {
    if (typeof o === 'function')
      o = {callback: o};
    // Set options
    var o = $.extend({
      direction: "left",
      distance: 20,
      times: 3,
      speed: 140,
      easing: "swing"
    }, o);

    return this.each(function() {

      // Create element
      var el = $(this), props = {
        position: el.css("position"),
        top: el.css("top"),
        bottom: el.css("bottom"),
        left: el.css("left"),
        right: el.css("right")
      };

      el.css("position", "relative");

      // Adjust
      var ref = (o.direction == "up" || o.direction == "down") ? "top" : "left";
      var motion = (o.direction == "up" || o.direction == "left") ? "pos" : "neg";

      // Animation
      var animation = {}, animation1 = {}, animation2 = {};
      animation[ref] = (motion == "pos" ? "-=" : "+=")  + o.distance;
      animation1[ref] = (motion == "pos" ? "+=" : "-=")  + o.distance * 2;
      animation2[ref] = (motion == "pos" ? "-=" : "+=")  + o.distance * 2;

      // Animate
      el.animate(animation, o.speed, o.easing);
      for (var i = 1; i < o.times; i++) { // Shakes
        el.animate(animation1, o.speed, o.easing).animate(animation2, o.speed, o.easing);
      };
      el.animate(animation1, o.speed, o.easing).
      animate(animation, o.speed / 2, o.easing, function(){ // Last shake
        el.css(props); // Restore
        if(o.callback) o.callback.apply(this, arguments); // Callback
      });
    });
  };
})(jQuery);
</script>
@stop
