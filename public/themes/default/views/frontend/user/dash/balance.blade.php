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
          <span class="balance-count block">{{ $sale_count }}</span>
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


  {{-- Pagination on top --}}
  {{ $transactions->links() }}

  @forelse($transactions as $transaction)
    {{-- Start Transaction details --}}
    <div class="listing m-t-10 hvr-grow-shadow2">

      {{-- Sale --}}
      @if($transaction->type == 'sale' || $transaction->type == 'refund')
        <div class="transaction-details bg-success">
          <i class="fa fa-plus"></i>
        </div>
      @endif

      {{-- Fee --}}
      @if($transaction->type == 'fee' || $transaction->type == 'withdrawal' || $transaction->type == 'purchase')
        <div class="transaction-details bg-danger">
          <i class="fa fa-minus"></i>
        </div>
      @endif

      {{-- Total transaction amount --}}
      <div class="transaction-details {{ ($transaction->type == 'sale' || $transaction->type == 'refund') ? 'text-success' : 'text-danger' }} bg-dark">
        {{ money(abs(filter_var(number_format($transaction->type == 'sale' ? $transaction->offer->listing->price/100 : $transaction->total,2), FILTER_SANITIZE_NUMBER_INT)), $transaction->currency)->format(true) }}
      </div>

      {{-- Show transaction details --}}
      <div class="listing-detail-wrapper">
        <div class="listing-detail">
          <div class="value condition">
            {{-- Transaction "Type" title --}}
            <div class="value-title">
              {{ trans('payment.transaction.type.type') }}
            </div>
            {{-- Transaction type --}}
            <div class="text">
              {{ trans('payment.transaction.type.' . $transaction->type) }}
            </div>
          </div>

          {{-- Transaction details for payment --}}
          @if($transaction->type == 'withdrawal')
            {{-- Payment method --}}
            <div class="value pickup condition">
              <div class="value-title">
                {{ trans('payment.withdrawal.status') }}
              </div>
              <div class="text">
                @php
                  switch ($transaction->withdrawal->status) {
                      case 0:
                          echo '<span class="bg-danger p-5 f-w-500 b-r f-s-12"><i class="fa fa-times" aria-hidden="true"></i> Declined</span>';
                          break;
                      case 1:
                          echo '<span class="bg-warning p-5 f-w-500 b-r f-s-12"><i class="fa fa-hourglass" aria-hidden="true"></i> Pending</span>';
                          break;
                      case 2:
                          echo '<span class="bg-success p-5 f-w-500 b-r f-s-12"><i class="fa fa-check" aria-hidden="true"></i> Complete</span>';
                          break;
                  }
                @endphp
              </div>
            </div>
            {{-- Payment method --}}
            <div class="value condition">
              <div class="value-title">
                {{ trans('payment.withdrawal.payment_method') }}
              </div>
              <div class="text">
                @if($transaction->withdrawal->payment_method == 'paypal')
                  <i class="fab fa-paypal"></i> PayPal
                @endif
                @if($transaction->withdrawal->payment_method == 'bank')
                  <i class="fas fa-money-check"></i> {{ trans('payment.withdrawal.bank_transfer') }}
                @endif
              </div>
            </div>
            {{-- Payment details --}}
            <div class="value condition">
              <div class="value-title">
                {{ trans('payment.withdrawal.details') }}
              </div>
              <div class="text">
                @if($transaction->withdrawal->payment_method == 'paypal')
                  {{ $transaction->withdrawal->payment_details }}
                @endif
                @if($transaction->withdrawal->payment_method == 'bank')
                  @php
                    $bank = json_decode($transaction->withdrawal->payment_details);
                  @endphp
                  {{ $bank->holder_name }} / {{ str_repeat('*', strlen($bank->iban) - 4) }}{{ substr($bank->iban, -4) }}
                @endif
              </div>
            </div>
          {{-- Transaction details for game --}}
          @else
            <div class="value pickup p-10">
              <div class="flex-center">
                {{-- Game Cover --}}
                <div class="m-r-10">
                  <span class="avatar">
                    <img src="{{ $transaction->item->listing->game->image_square_tiny }}" alt="{{ $transaction->item->listing->game->name }}">
                  </span>
                </div>
                {{-- Game Name + platform --}}
                <div>
                  <div class="title text-white f-w-500">{{ $transaction->item->listing->game->name }}</div>
                  <span class="platform-label" style="background-color:{{ $transaction->item->listing->game->platform->color }};"> {{ $transaction->item->listing->game->platform->name }} </span>
                </div>
              </div>
            </div>
          @endif

        </div>
      </div>

      {{-- Details Button --}}
      @if($transaction->type == 'fee' || $transaction->type == 'sale' || $transaction->type == 'purchase' || $transaction->type == 'refund')
        <a href="{{ url('offer/' . $transaction->item_id) }}">
          <div class="details-button">
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
            <span class="hidden-sm-down"> {{ trans('listings.overview.subheader.details') }}</span>
          </div>
        </a>
      @endif
    </div>
    {{-- End Listing Details --}}
    {{-- Start user info and creation date --}}
    <div class="listing-user-details flex-center-space">
      <div>
        @if($transaction->type == 'sale')
        <a href="{{$transaction->payer->url}}" class="user-link">
          <span class="avatar avatar-xs @if($transaction->payer->isOnline()) avatar-online @else avatar-offline @endif">
            <img src="{{ $transaction->payer->avatar_square_tiny }}" alt="{{$transaction->payer->name}}'s Avatar"><i></i>
          </span>
          {{$transaction->payer->name}}
        </a>
        @endif
        @if($transaction->type == 'purchase' || $transaction->type == 'refund')
        <a href="{{$transaction->offer->listing->user->url}}" class="user-link">
          <span class="avatar avatar-xs @if($transaction->offer->listing->user->isOnline()) avatar-online @else avatar-offline @endif">
            <img src="{{ $transaction->offer->listing->user->avatar_square_tiny }}" alt="{{$transaction->offer->listing->user->name}}'s Avatar"><i></i>
          </span>
          {{$transaction->offer->listing->user->name}}
        </a>
        @endif
        @if($transaction->type == 'fee' || $transaction->type == 'withdrawal')
          <i class="fa fa-suitcase" aria-hidden="true"></i> {{config('settings.page_name')}}
        @endif
      </div>
      <div>
        <i class="fa fa-clock-o" aria-hidden="true"></i> {{$transaction->created_at->diffForHumans()}}
      </div>
    </div>
    {{-- End user info and creation date --}}

  @empty
    {{-- Start empty list message --}}
    <div class="empty-list add-button">
      {{-- Icon --}}
      <div class="icon">
        <i class="far fa-frown" aria-hidden="true"></i>
      </div>
      {{-- Text --}}
      <div class="text">
        {{ trans('payment.no_transactions') }}
      </div>
    </div>
    {{-- End empty list message --}}
  @endforelse

  {{-- Pagination on bottom --}}
  <div class="m-t-10">
    {{ $transactions->links() }}
  </div>

@stop
