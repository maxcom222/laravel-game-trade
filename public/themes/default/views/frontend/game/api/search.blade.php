{{-- Trade style fix --}}
@if($trade_search) <div style="height: 20px"></div> @endif
@forelse($json_results as $result)
  @php
    //Check if game have release date
    if(isset($result->rlsdate)) {
      Carbon::setLocale('de');
      $release = $result->rlsdate;
      $dt = Carbon::parse($release);
      $release_date = $dt->formatLocalized('%d. %B %Y');
    }else{
      $release = 0;
    }

    //Check if game exist in database
    $game = \App\Models\Game::whereHas('metacritic', function ($query) use ($result) {
    $query->where('url', $result->url);
    })->with('metacritic')->first();

  @endphp

  <section class="panel">

    <div class="panel-body">
      <div class="flex-center">
        {{-- Game Cover --}}
        <div class="m-r-20">
          <span class="avatar avatar-lg">
            @if($game)
            <img src="{{ $game->image_square_tiny }}" alt="{{ $game->name }}">
            @else
            <img src="{{ asset('images/square_tiny/no_cover.jpg') }}" alt="Not in database">
            @endif
          </span>
        </div>
        {{-- Game title & platform --}}
        <div>
          <div class="game-title">{{ $game ? $game->name : $result->name }}</div>
          <div class="game-labels">
            <span class="platform-label m-r-5" style="background-color:{{ $platform->color }};">{{ $platform->name }}</span>
            @if($release && $result->rlsdate != '0-01-01')
              <span><i class="fa fa-calendar"></i> {{ $release_date }} </span>
            @endif
          </div>
        </div>
      </div>
    </div>


    <div class="panel-footer">
      {{-- Database status --}}
      <div class="in-database">
        {{ trans('games.add.results.in_database') }} <i class="fa {{ $game ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}" aria-hidden="true"></i>
      </div>

      @if($trade_search)
        {{-- Add to tradelist link for trade search --}}
        @if($game)
        <a href="javascript:void(0)" class="button to-tradelist" id="{{ $game->id }}">
          <i class="fa fa-arrow-right" aria-hidden="true"></i> {{ trans('games.add.results.add_tradelist') }}
        </a>
        {{-- Add to database link for trade search --}}
        @else
        <form id="gameAdd-{{$loop->iteration}}" method="POST" novalidate="novalidate">
          <input type="hidden" name="platform" value="{{ $platform->acronym }}">
          <input type="hidden" name="value" value="{{ $result->name }}">
          <a href="javascript:void(0)" class="button add-game to-database"  data-id="{{$loop->iteration}}">
            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('games.add.results.add_database') }}
          </a>
        </form>
        @endif

      @else
        {{-- Details link for normal search --}}
        @if($game)
        <a href="{{ $game->url_slug }}" class="button">
          <i class="fa fa-arrow-right" aria-hidden="true"></i> {{ trans('games.add.results.details') }}
        </a>
        {{-- Add game link for normal search --}}
        @else
        <form id="gameAdd-{{$loop->iteration}}" method="POST" novalidate="novalidate">
          <input type="hidden" name="platform" value="{{ $platform->acronym }}">
          <input type="hidden" name="value" value="{{ $result->name }}">
          <a href="javascript:void(0)" class="button add-game" data-id="{{$loop->iteration}}">
            <i class="fa fa-plus" aria-hidden="true"></i> {{ trans('games.add.add_game') }}
          </a>
        </form>


        @endif

      @endif
    </div>

  </section>

@empty

  {{-- No search reults --}}
  <section class="panel">

    <div class="panel-body" style="text-align: center;">

      <i class="far fa-frown m-b-10" aria-hidden="true" style="font-size: 100px;"></i>
      <div>{{ trans('games.add.results.no_results', ['value' => $value]) }}</div>

    </div>

  </div>

@endforelse

<script type="text/javascript">
$(document).ready(function(){
{{-- Start JS for trade search --}}
@if($trade_search)
  const autoNumericOptions = {
      digitGroupSeparator        : '{{ Currency(Config::get('settings.currency'))->getThousandsSeparator() }}',
      decimalCharacter           : '{{ Currency(Config::get('settings.currency'))->getDecimalMark() }}',
  };

  $(".to-tradelist").click(function(e){
    e.preventDefault();

    $.ajax({
      type: 'GET',
      url: '{{ url('api/games') }}/' + $(this).attr('id'),
      dataType: 'json',
      success: function (data) {
        var customTags = [ '<%', '%>' ];
        Mustache.tags = customTags;
        var template = $('#template').html();
        Mustache.parse(template);   // optional, speeds up future uses
        var append_date = Mustache.render(template, data);
        $(append_date).hide().appendTo('.trade_list').slideDown("fast", function() {
          $('#tradesearch').validate();
        });
        {{-- Enable tooltip on new game element --}}
        $('[data-toggle="tooltip"]').tooltip();
        {{-- Enable maskMoney on new game element --}}
        $('.get_price').autoNumeric('init', autoNumericOptions);
        setTimeout(function(){$('#tradesearch').typeahead('val', ''); }, 10);
        $('#TradeGameAdd').modal('toggle');

        $('#TradeGameAdd').on('hidden.bs.modal', function (e) {
          $('#searchresult').html('');
          $('#appendedInput').val('');
        });

      }
    });

  });


  $(".to-database").click(function(e){
    e.preventDefault();

    var ajaxSucceeded = false;

    $('#search_footer').fadeOut('fast');


    $('.main-content').slideUp("fast").promise().done(function(){
      $('.loading').slideDown("fast");
    });

    $('#TradeGameAdd').on('hide.bs.modal', function (e) {
      if (!ajaxSucceeded) {
          return false;
      }
    });

    var id = $(this).data('id');

    $.ajax({
        url:'{{ url("games/add/json") }}',
        type: 'POST',
        data:$('#gameAdd-' + id).serialize(),
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        dataType: 'json',
        success: function (data) {
          ajaxSucceeded = true;
          var customTags = [ '<%', '%>' ];
          Mustache.tags = customTags;
          var template = $('#template').html();
          Mustache.parse(template);   // optional, speeds up future uses
          var append_date = Mustache.render(template, data);
          $(append_date).hide().appendTo('.trade_list').slideDown("fast", function() {
            $('#tradesearch').validate();
          });
          {{-- Enable tooltip on new game element --}}
          $('[data-toggle="tooltip"]').tooltip();
          {{-- Enable maskMoney on new game element --}}
          $('.get_price').autoNumeric('init', autoNumericOptions);
          setTimeout(function(){$('#tradesearch').typeahead('val', ''); }, 10);
          $('#TradeGameAdd').modal('hide');
          $('#TradeGameAdd').on('hidden.bs.modal', function (e) {
            $('#searchresult').html('');
            $('#appendedInput').val('');
            $('#search_footer').fadeIn('fast');
            $('.main-content').slideDown("fast").promise().done(function(){
              $('.loading').slideUp("fast");
            });
          });
        }
    });



  });

@else
{{-- End JS for trade search --}}


  {{-- Open loading modal on game add --}}
  $(".add-game").click(function(event){
    var id = $(this).data('id');

    $('#modal_game_add').modal('show');
    setTimeout(function(){
      $.ajax({
          url:'{{ url("games/add") }}',
          type: 'POST',
          data:$('#gameAdd-' + id).serialize(),
          {{-- Send CSRF Token over ajax --}}
          headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
          success: function (data) {
            window.location=data;
          }
      });
     },500)

    // override browser following link when clicked
    return false;
  });
@endif
})
</script>
