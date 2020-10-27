{{-- Start Modal for new messages --}}
<div class="modal fade modal-fade-in-scale-up modal-primary" id="NewMessage" tabindex="-1" role="dialog">
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
            <i class="fas fa-envelope-open m-r-5"></i>
            <strong>{{ trans('messenger.new_message') }}</strong>
          </h4>
        </div>
      </div>

      {{-- Open new form for adding a new message --}}
      {!! Form::open(array('url'=>'messages', 'id'=>'form-new-message', 'role'=>'form')) !!}

      <div class="modal-body">

        {{-- Show selected user --}}
        <div class="selected-user {{ !isset($user) ? 'hidden' : 'flex-center-space' }}" id="selected-user">
          {{-- Check if user is already defined --}}
          @if(isset($user))
            {{-- User infos (avatar and name) --}}
            <div class="selected-user-info">
              <span class="avatar @if($user->isOnline()) avatar-online @else avatar-offline @endif m-r-10" style="vertical-align: inherit !important;"><img src="{{ $user->avatar_square_tiny }}" class="img-circle"><i></i></span><input name="recipient" type="hidden" value="{{ $user->id }}"> {{ $user->name }}
            </div>
          @else
            <div class="flex-center-space">
              {{-- User infos (avatar and name) --}}
              <div class="selected-user-info"></div>
              {{-- Reselect button --}}
              <a href="javascript:void(0)" id="reselect-user" class="btn btn-dark"><i class="fa fa-repeat"></i></a>
            </div>
          @endif
        </div>


        <!-- Start  Main Content -->
        <div class="main-content">

          {{-- Check if user is already defined --}}
          @if(!isset($user))
            {{-- Input group for user search --}}
            <div id="select-user">
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
                <input type="text" class="form-control rounded input-lg inline input" id="offersearch" autocomplete="off">
              </div>
            </div>
          @endif

          <div id="user-selected" class="{{ isset($user) ? '' : 'hidden'}}">
            {{-- Textare for message input --}}
            <textarea id="message-textarea" autocomplete="off" class="form-control input m-t-10" rows="5"  name="message" placeholder="{{ trans('offers.general.enter_message') }}"></textarea>
          </div>

        </div>
        <!-- End Main Content -->

      </div>
      {!! Form::close() !!}
      {{-- Close new form for adding a new message --}}

      <div class="modal-footer" id="search_footer">
        {{-- Close button --}}
        <a href="javascript:void(0)" data-dismiss="modal" data-bjax class="btn btn-dark btn-lg">{{ trans('listings.modal.close') }}</a>
        {{-- Submit button --}}
        <button id="send-message" class="btn btn-primary btn-animate btn-animate-vertical btn-lg {{ isset($user) ? '' : 'hidden'}}" type="submit">
          <span><i class="icon fas fa-paper-plane" aria-hidden="true"></i> {{ trans('general.send') }}
          </span>
        </button>
        {{-- Please select user button --}}
        <button class="error-search btn btn-light btn-lg {{ isset($user) ? 'hidden' : ''}}" type="button" disabled>
          {{ trans('messenger.select_user') }}
        </button>
      </div>

    </div>
  </div>
</div>
{{-- End Modal for for new messages --}}


@push('scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- Check if user is already defined --}}
  @if(!isset($user))
    {{-- Start typeahead for user search --}}
    {{-- Bloodhound engine with remote search data in json format --}}
    $('#offersearch').submit(false);
    var listingGameSearch = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        url: '{{ url("user/search/json/%QUERY") }}',
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
        empty: [
          '<div class="nosearchresult bg-danger">',
            '<span><i class="fa fa-ban"></i> {{ trans('messenger.no_user_found') }}<span>',
          '</div>'
        ].join('\n'),
        suggestion: function (data) {
          return '<div class="searchresult hvr-grow-shadow2"><span class="link"><div class="inline-block m-r-10"><span class="avatar avatar-'+ data.status +'" style="vertical-align: inherit !important;"><img src="' + data.avatar + '" class="img-circle"><i></i></span></div><strong class="title">' + data.name + '</strong></span></div>';
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
    {{-- End typeahead for user search --}}

    {{-- Change selected user on selecting typeahead --}}
    $('#offersearch').bind('typeahead:selected', function(obj, datum, name) {
      $('.selected-user-info').html('<span class="avatar avatar-'+ datum.status +' m-r-10" style="vertical-align: inherit !important;"><img src="' + datum.avatar + '" class="img-circle"><i></i></span><input name="recipient" type="hidden" value="' + datum.id +'">' + datum.name);
      $('#select-user').slideUp(300);
      $('#user-selected').slideDown(300);
      $('#selected-user').slideDown(300);
      $('.error-search').fadeOut(200).promise().done(function(){
        $('#send-message').fadeIn(200);
      });
    });

    {{-- Reset user --}}
    $('#reselect-user').click(function(e) {
      e.preventDefault();
      $('#send-message').fadeOut(200).promise().done(function(){
        $('.error-search').fadeIn(200);
      });
      $('.selected-user-info').html('');
      $('#selected-user').slideUp(300);
      $('#select-user').slideDown(300);
      $('#user-selected').slideUp(300);
    });
  @endif

  {{-- Message submit --}}
  $("#send-message").click( function(){
    $('#send-message span').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#send-message').addClass('loading');
    $('#form-new-message').submit();
  });

});
</script>
@endpush
