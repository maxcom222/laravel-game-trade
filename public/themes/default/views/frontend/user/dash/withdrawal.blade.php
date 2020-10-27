@extends(Theme::getLayout())

@section('subheader')

  <div class="subheader tabs">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    {{-- Subheader title (Listings) --}}
    <div class="content">
      {{-- Balance details --}}
      <div class="flex-center-space balance-wrapper">
        {{-- Show available balance --}}
        <div>
          {{-- Available balance --}}
          <span class="balance-count block">{{ money(abs(filter_var(number_format(Auth::user()->balance ,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</span>
          {{-- Balance text --}}
          <span class="balance-text">{{ trans('payment.available_balance') }}</span>
        </div>
        {{-- Show sales --}}
        <div class="text-right">
          {{-- Sale count --}}
          <span class="balance-count block">{{ $transactions->where('type','sale')->count() }}</span>
          {{-- Sale text --}}
          <span class="balance-text">{{ trans('payment.sales') }}</span>
        </div>
      </div>
    </div>

    <div class="tabs">
      {{-- Transactions tab --}}
      <a class="tab {{  Request::is('dash/balance') ? 'active' : ''}}"  href="{{url('dash/balance')}}">
        {{ trans('payment.transactions') }}
      </a>

      @if(Auth::user()->balance > 0)
      {{-- Balance tab --}}
      <a class="tab {{  Request::is('dash/balance/withdrawal') ? 'active' : ''}}"  href="{{url('dash/balance/withdrawal')}}">
        {{ trans('payment.withdrawal.withdrawal') }}
      </a>
      @endif

    </div>

  </div>

@stop


@section('content')

  <div class="withdrawal">
    {{-- Payment methods --}}
    <div class="m-b-20">

      <ul class="subheader-tabs" role="tablist">
        @if(config('settings.withdrawal_paypal'))
        {{-- PayPal Method --}}
        <li class="nav-item">
          <a data-toggle="tab" href="#paypal" data-target="#paypal" role="tab" class="subheader-link @if(!($errors->has('bank_holder_name') || $errors->has('bank_iban') || $errors->has('bank_bic') || $errors->has('bank_name'))) active @endif">
            <i class="fab fa-paypal f-w-500"></i> PayPal
          </a>
        </li>
        @endif
        @if(config('settings.withdrawal_bank'))
        {{-- Bank Transfer Method --}}
        <li class="nav-item">
          <a data-toggle="tab" href="#bank" data-target="#bank" role="tab" class="subheader-link @if(!config('settings.withdrawal_paypal') || config('settings.withdrawal_paypal') && ($errors->has('bank_holder_name') || $errors->has('bank_iban') || $errors->has('bank_bic') || $errors->has('bank_name'))) active @endif">
            <i class="fas fa-money-check"></i> {{ trans('payment.withdrawal.bank_transfer') }}
          </a>
        </li>
        @endif
      </ul>
    </div>
    <div class="tab-content">

      {{-- PayPal Tab --}}
      @if(config('settings.withdrawal_paypal'))
      <div class="tab-pane fade @if(!($errors->has('bank_holder_name') || $errors->has('bank_iban') || $errors->has('bank_bic') || $errors->has('bank_name'))) show active in @endif" id="paypal" role="tabpanel">
        {!! Form::open(array('url'=>'dash/balance/withdrawal/paypal', 'id'=>'form-withdrawal-paypal' )) !!}
        {{-- Start Details Panel --}}
        <section class="panel">
          {{-- Panel Title (Details) --}}
          <div class="panel-heading">
            <h3 class="panel-title">{{ trans('payment.withdrawal.withdrawal_details') }}</h3>
          </div>

          <div class="panel-body">
            <div class="form-group">
              {{-- PayPal email address label --}}
              <label>
                {{ trans('payment.withdrawal.paypal_email') }} <strong><span class="text-danger">*</span></strong>
              </label>
              @if($errors->has('paypal_email'))
                {{-- Email error msg --}}
                <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('paypal_email') }}
                </div>
              @endif
              {{-- Input group for paypal address --}}
              <div class="input-group input-group-lg {{$errors->has('paypal_email') ? 'has-error' : '' }}">
                <span class="input-group-addon">
                  <span><i class="fab fa-paypal"></i></span>
                </span>
                {{-- PayPal address Input --}}
                {{ Form::input('paypal_email', 'paypal_email', null, ['class' => 'form-control rounded input-lg inline input', 'placeholder' => trans('payment.withdrawal.paypal_email')]) }}
              </div>
            </div>
            <div class="form-group">
              {{-- Amount label --}}
              <label>
                {{ trans('payment.withdrawal.amount') }}
              </label>
              {{-- Withdrawal amount --}}
              <div class="withdrawal-amount">
                {{ money(abs(filter_var(number_format(Auth::user()->balance ,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}
              </div>
            </div>
          </div>
        </section>
        {{-- Submit button --}}
        <div class="float-right">
          <button class="btn btn-lg btn-success" type="submit" id="submit-button-paypal"><i class="fab fa-paypal"></i> {{ trans('payment.withdrawal.submit_request') }}</button>
        </div>
        {!! Form::close() !!}
      </div>
      @endif


      {{-- Bank Transfer Tab --}}
      @if(config('settings.withdrawal_bank'))
      <div class="tab-pane fade @if(!config('settings.withdrawal_paypal') || config('settings.withdrawal_paypal') && ($errors->has('bank_holder_name') || $errors->has('bank_iban') || $errors->has('bank_bic') || $errors->has('bank_name'))) show active in @endif" id="bank" role="tabpanel">
        {!! Form::open(array('url'=>'dash/balance/withdrawal/bank', 'id'=>'form-withdrawal-bank' )) !!}
        {{-- Start Details Panel --}}
        <section class="panel">
          {{-- Panel Title (Details) --}}
          <div class="panel-heading">
            <h3 class="panel-title">{{ trans('payment.withdrawal.withdrawal_details') }}</h3>
          </div>

          <div class="panel-body">
            <div class="form-group">
              {{-- Bank holder name label --}}
              <label>
                {{ trans('payment.withdrawal.bank.holder_name') }} <strong><span class="text-danger">*</span></strong>
              </label>
              @if($errors->has('bank_holder_name'))
                {{-- Bank holder name error msg --}}
                <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('bank_holder_name') }}
                </div>
              @endif
              {{-- Input group for bank holder name --}}
              <div class="input-group input-group-lg {{$errors->has('bank_holder_name') ? 'has-error' : '' }}">
                <span class="input-group-addon">
                  <span><i class="fas fa-id-card"></i></span>
                </span>
                {{-- Bank holder name Input --}}
                {{ Form::input('bank_holder_name', 'bank_holder_name', null, ['class' => 'form-control rounded input-lg inline input', 'placeholder' => trans('payment.withdrawal.bank.holder_name')]) }}
              </div>
            </div>
            <div class="form-group">
              {{-- IBAN label --}}
              <label>
                {{ trans('payment.withdrawal.bank.iban') }} <strong><span class="text-danger">*</span></strong>
              </label>
              @if($errors->has('bank_iban'))
                {{-- IBAN error msg --}}
                <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('bank_iban') }}
                </div>
              @endif
              {{-- Input group for IBAN --}}
              <div class="input-group input-group-lg {{$errors->has('bank_iban') ? 'has-error' : '' }}">
                <span class="input-group-addon">
                  <span><i class="fas fa-money-check"></i></span>
                </span>
                {{-- IBAN Input --}}
                {{ Form::input('bank_iban', 'bank_iban', null, ['class' => 'form-control rounded input-lg inline input', 'placeholder' => trans('payment.withdrawal.bank.iban')]) }}
              </div>
            </div>
            <div class="form-group">
              {{-- Bic label --}}
              <label>
                {{ trans('payment.withdrawal.bank.bic') }} <strong><span class="text-danger">*</span></strong>
              </label>
              @if($errors->has('bank_bic'))
                {{-- Bic error msg --}}
                <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('bank_bic') }}
                </div>
              @endif
              {{-- Input group for Bic --}}
              <div class="input-group input-group-lg {{$errors->has('bank_bic') ? 'has-error' : '' }}">
                <span class="input-group-addon">
                  <span><i class="fas fa-money-check"></i></span>
                </span>
                {{-- Bic Input --}}
                {{ Form::input('bank_bic', 'bank_bic', null, ['class' => 'form-control rounded input-lg inline input', 'placeholder' => trans('payment.withdrawal.bank.bic')]) }}
              </div>
            </div>
            <div class="form-group">
              {{-- Bank name label --}}
              <label>
                {{ trans('payment.withdrawal.bank.bank_name') }} <strong><span class="text-danger">*</span></strong>
              </label>
              @if($errors->has('bank_name'))
                {{-- Bank name error msg --}}
                <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
                  <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('bank_name') }}
                </div>
              @endif
              {{-- Input group for Bank name --}}
              <div class="input-group input-group-lg {{$errors->has('bank_name') ? 'has-error' : '' }}">
                <span class="input-group-addon">
                  <span><i class="fas fa-university"></i></span>
                </span>
                {{-- Bank name Input --}}
                {{ Form::input('bank_name', 'bank_name', null, ['class' => 'form-control rounded input-lg inline input', 'placeholder' => trans('payment.withdrawal.bank.bank_name')]) }}
              </div>
            </div>
            <div class="form-group">
              {{-- Amount label --}}
              <label>
                {{ trans('payment.withdrawal.amount') }}
              </label>
              {{-- Withdrawal amount --}}
              <div class="withdrawal-amount">
                {{ money(abs(filter_var(number_format(Auth::user()->balance ,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}
              </div>
            </div>
          </div>
        </section>
        {{-- Submit button --}}
        <div class="float-right">
          <button class="btn btn-lg btn-success" type="submit" id="submit-button-bank"><i class="fas fa-money-check"></i> {{ trans('payment.withdrawal.submit_request') }}</button>
        </div>
        {!! Form::close() !!}
      </div>
      @endif

    </div>
  </div>

@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- PayPal submit --}}
  $('#submit-button-paypal').click( function(){
    $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $(this).addClass('loading');
    $('#form-withdrawal-paypal').submit();
  });
  {{-- Bank Transfer submit --}}
  $('#submit-button-bank').click( function(){
    $(this).html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $(this).addClass('loading');
    $('#form-withdrawal-bank').submit();
  });
});
</script>

@endsection

@stop
