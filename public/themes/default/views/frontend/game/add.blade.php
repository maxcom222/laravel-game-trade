@extends(Theme::getLayout())

@section('subheader')
  {{-- Start Subheader --}}
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
    <div class="background-color"></div>

    <div class="content">
      <span class="title"><i class="fa fa-plus"></i> {{ trans('games.add.add_game') }}</span>
    </div>

  </div>
  {{-- End Subheader --}}
@stop


@section('content')
  {{-- Start Select Game Panel --}}
  <section class="panel">

    {{-- Panel Header --}}
    <div class="panel-heading">
      <h3 class="panel-title">
        <i class="fa fa-search"></i> {{ trans('games.add.search_game') }}
      </h3>
    </div>
    {{-- Open form for search --}}
    <form id="searchForm" method="POST" novalidate="novalidate">

      <div class="panel-body">

        {{-- Loading bar --}}
        <div class="loading-bar hidden" id="loading_bar">
          <i class="fa fa-spinner fa-pulse fa-fw"></i> {{ trans('games.add.searching') }}
        </div>

        {{-- Start Input Group with system select and input for search value --}}
        <div class="input-group input-group-lg" id="search_bar">
          <div class="input-group-btn search-panel">
            {{-- Select for systems --}}
            <button type="button" id="platform_select" class="btn dropdown-toggle dropdown-system dropup" data-toggle="dropdown">
              <span id="search_concept">{{ trans('games.add.select_system') }}</span> <span class="caret"></span>
            </button>
            @php
              $api_platforms = ['pc','ios','dreamcast','ps','ps2','ps3','ps4','psp','vita','xbox','xbox360','xboxone','gba','ds','3ds','gamecube','n64','wii','wii-u','switch'];
              $platforms = \App\Models\Platform::whereIn('acronym', $api_platforms)->get();
            @endphp
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
          {{-- Search param - in this case system acronym --}}
          <input type="hidden" name="search_param" value="all" id="search_param">
          {{-- Input for search value --}}
          <input type="text" id="appendedInput" name="game" class="form-control input" name="x" placeholder="{{ trans('games.add.enter_title') }}" autocomplete="off">
        </div>
        {{-- End Input Group with system select and input for search value --}}
      </div>

      <div class="panel-footer">
        <div></div>
        {{-- Form submit --}}
        <button type="submit" class="button send-search" id="startsearch" style="display: none;" disabled>
          <i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}
        </button>
        {{-- Message, when no system is selected --}}
        <a href="javascript:void(0);" class="button error-search" id="startsearch">
          <i class="fa fa-times-circle" aria-hidden="true"></i> {{ trans('games.add.select_system_info') }}
        </a>
      </div>

    </form>
    {{-- Close form for search --}}

  </section>
  {{-- End Select Game Panel --}}



  <div id="searchresult">
  </div>

  {{-- Start Loading Modal --}}
  <div class="modal fade modal-fade-in-scale-up" id="modal_game_add" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-body modal-loading">
          <div class="loader-item"><div class="loader pacman-loader lg"></div></div>
          <span>
              <strong>{{ trans('games.add.adding',  ['pagename' =>Config::get('settings.page_name')]) }}</strong> <br> <span id="please_wait">{{ trans('games.add.wait') }}</span>
          </span>
        </div>
      </div>
    </div>
  </div>
  {{-- End Loading Modal --}}



@stop


@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){
  {{-- Center loading modal --}}
  (function ($) {
      "use strict";
      function centerModal() {
          $(this).css('display', 'block');
          var $dialog  = $(this).find(".modal-dialog"),
          offset       = ($(window).height() - $dialog.height()) / 2,
          bottomMargin = parseInt($dialog.css('marginBottom'), 10);
          if(offset < bottomMargin) offset = bottomMargin;
          $dialog.css("margin-top", offset);
      }

      $(document).on('show.bs.modal', '.modal', centerModal);
      $(window).on("resize", function () {
          $('.modal:visible').each(centerModal);
      });
  }(jQuery));

  {{-- Loading dots animation --}}
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

  {{-- Change background color of dropdown on system select --}}
  var platform = "no";
  $('.search-panel .dropdown-menu').find('a').click(function(e) {
		e.preventDefault();
        platform = $(this).attr("href").replace("#","");
        color = $(this).data("color");
		var concept = $(this).text();
		$('.search-panel span#search_concept').text(concept);
		$('.input-group #search_param').val(platform);
    $('#platform_select').css("background-color", color );

    {{-- Check if system is selected --}}
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

  {{-- Send CSRF Token over ajax --}}
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': Laravel.csrfToken }
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
            $(".send-search").html('<i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}');

          },
          error: function (data) {
            alert('Oops, an error occurred!')
            $('#loadingoffercomplete').show();
            $('#loadingoffersearch').hide();

            $('#loading_bar').fadeOut(300).promise().done(function(){
                 $('#search_bar').fadeIn(200);
            });
            $('.send-search').attr('disabled', false);
            $(".send-search").html('<i class="fa fa-search" aria-hidden="true"></i> {{ trans('general.search') }}');
          }
      });
    }
  });
  {{-- End Form submit and get ajax results --}}


})
</script>
@stop
