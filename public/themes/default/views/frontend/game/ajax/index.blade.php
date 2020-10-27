  {{-- Start Filter / Sort options --}}
  <div class="m-b-20 flex-center-space">
      {{-- Start Filter button --}}
      <div>
          {{-- Filter Button with active filter count - open modal --}}
          <a href="#" data-toggle="modal" data-target="#modal_filter" class="btn btn-dark">
              <i class="fa fa-filter" aria-hidden="true"></i> {{ trans('general.sortfilter.filter') }} @if(session()->get('listingsPlatformFilter') || session()->has('listingsOptionFilter')) ({{ ( session()->has('listingsPlatformFilter') ? count(session()->get('listingsPlatformFilter')) : 0) + ( session()->has('listingsOptionFilter') ? count(session()->get('listingsOptionFilter')) : 0)}}) @endif
          </a>
          {{-- Remove button - only visible with active filters --}}
          @if(session()->has('listingsPlatformFilter') || session()->has('listingsOptionFilter'))
          <a id="remove-filter" href="{{ url('listings/filter/remove') }}" class="m-l-5 btn btn-dark">
              <i class="fa fa-times" aria-hidden="true"></i>
          </a>
          @endif
      </div>
      {{-- End Filter button --}}
      {{-- Start sort options --}}
      <div>
          {{-- Sort order button (desc / asc) --}}
          <a id="order-direction" href="{{ url('games/order') }}/{{ session()->has('gamesOrder') ? session()->get('gamesOrder') : 'release_date' }}/{{  session()->has('gamesOrderByDesc') ? (session()->get('gamesOrderByDesc') ? 'asc' : 'desc') : 'asc' }}" class="btn btn-dark" style="vertical-align: inherit;">
              <i class="fa fa-sort-amount-{{ session()->has('gamesOrderByDesc') ? (session()->get('gamesOrderByDesc') ? 'up' : 'down') : 'up' }}" aria-hidden="true"></i>
          </a>
          {{-- Sort dropdown --}}
          <div class="m-l-5 inline-block">
              <select id="order_by" class="form-control select" style="height: 33px !important;">
                  {{-- Sort by --}}
                  <option disabled>{{ trans('general.sortfilter.sort_by') }}</option>
                  {{-- Release option --}}
                  <option value="{{ url('games/order/release_date') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'created_at' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_release') }}</option>
                  {{-- Metascore option --}}
                  <option value="{{ url('games/order/metascore') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'metascore' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_metascore') }}</option>
                  {{-- Listings option --}}
                  <option value="{{ url('games/order/listings') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'listings' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_listings') }}</option>
                  {{-- Popularity option --}}
                  <option value="{{ url('games/order/popularity') }}" {{ session()->has('gamesOrder') ? (session()->get('gamesOrder') == 'popularity' ? 'selected' : '') : '' }}>{{ trans('general.sortfilter.sort_popularity') }}</option>
              </select>
          </div>
      </div>
      {{-- End sort options --}}
  </div>
  {{-- End Filter / Sort options --}}


  {{-- START GAME LIST --}}
  <div class="row">
    @forelse ($games as $game)
      @include('frontend.game.inc.card')
    @empty
      {{-- Start empty list message --}}
      <div class="empty-list">
        {{-- Icon --}}
        <div class="icon">
          <i class="far fa-frown" aria-hidden="true"></i>
        </div>
        {{-- Text --}}
        <div class="text">
          {{ trans('games.overview.no_games') }}
        </div>
      </div>
      {{-- End empty list message --}}
    @endforelse


  </div>
  {{-- END GAME LIST --}}

  {{ $games->links() }}

  <script type="text/javascript">
  $(document).ready(function(){
    {{-- Change current page --}}
    $('#current-page').html('{{$games->currentPage()}}');

    {{-- Change Last page --}}
    $('#last-page').html('{{$games->lastPage()}}');

    {{-- Change URL in browser history --}}
    if (typeof (history.pushState) != "undefined") {
      var url = '{{ ($games->currentPage() == 1 ? url('games') : url('games?page='.$games->currentPage())) }}';
      history.pushState(null, $(document).find("title").text(), url);
    }

    {{-- AJAX Pagination --}}
    $(".pagination a").click(function(e) {
      e.preventDefault();
      {{-- Add spinner icon to the pagination link --}}
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw" style="margin-right: -3px; margin-left: -5px;"></i>');
      {{-- Get URL from link --}}
      var url = $(this).attr('href');
      ajaxLoad(url);
    });

    function ajaxLoad(url, callback) {
      {{-- Load URL through AJAX --}}
      $.ajax({
        {{-- Set load progress bar width to 10% before load for smoother animation --}}
        beforeSend: function () {
          $('.load-progress-animation').removeClass('hide');
          $('.load-progress').css({
            width:'10%'
          });
        },
        {{-- Update progress bar width during loading --}}
        xhr: function () {
          var xhr = new window.XMLHttpRequest();
          {{-- Event listener for loading the URL --}}
          xhr.addEventListener("progress", function (evt) {
            if (evt.lengthComputable) {
              {{-- Get percantage of complete loading --}}
              var percentComplete = evt.loaded / evt.total;
              {{-- Add the complete loading to the loading bar CSS --}}
              $('.load-progress').css({
                width: percentComplete * 100 + '%'
              });
              {{-- Remove loading bar if URL loaded --}}
              if (percentComplete === 1) {
                $('html, body').scrollTop(0);
                $('.load-progress-animation').addClass('hide');
                $('.load-progress').css({
                  width: '0%'
                });
              }
            }
          }, false);
          return xhr;
        },
        url: url,
        success: function(data) {
          {{-- Reset progress bar if XHR is not supported --}}
          $('html, body').scrollTop(0);
          $('.load-progress').css({
            width:  '100%'
          });
          $('.load-progress-animation').addClass('hide');
          $('.load-progress').css({
            width: '0%'
          });
          {{-- Change HTML with newly loaded HTML --}}
          $('#games-wrapper').html(data);
          {{-- Reset loading bar after hide animation (1.2s) --}}
          if (typeof callback === "function") {
            callback();
          }
        }
      });
    }

    {{-- Order by change URL --}}
    $('#order_by').change(function () {
        var goToUrl = $(this).val();
        ajaxLoad(goToUrl);
    });

    {{-- Remove all active filter --}}
    $('#remove-filter').click(function (e) {
      e.preventDefault();
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      ajaxLoad($(this).attr('href'), function() {
        $(".platform-filter-active").css("background-color", "");
        $(".platform-filter").removeClass("platform-filter-active");
      });
    });

    {{-- Change order direction --}}
    $('#order-direction').click(function (e) {
      e.preventDefault();
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      ajaxLoad($(this).attr('href'));
    });

    $("div.lazy").lazyload({
        effect : "fadeIn",
        load : function(elements_left, settings) {
          $(this).parent().parent().find('.pacman-loader').delay(200).fadeOut();
        }
    });
    $('.col-xs-6').matchHeight();

  });
  </script>
