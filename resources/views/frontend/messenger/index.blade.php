@extends('frontend.layouts.app')

@section('content')
  {{--
    @include('frontend.messenger.partials.flash')

    @each('frontend.messenger.partials.thread', $threads, 'thread', 'frontend.messenger.partials.no-threads') --}}



    <!-- Page -->
    <div class="page-aside-left">
      <!-- Message Sidebar -->
      <div class="page-aside closed">
        <div class="page-aside-switch">
          <i class="fas fa-chevron-right"></i>
        </div>
        <div class="page-aside-inner">

          <div class="app-message-list page-aside-scroll">
            <div data-role="container">
              <div data-role="content">
                <ul class="list-group">
                  {{-- New message button --}}
                  <li class="new-message m-b-5"><a href="javascript:void(0)" data-toggle="modal" data-target="#NewMessage" class="btn btn-primary btn-block "><i class="fas fa-envelope-open m-r-5"></i>{{ trans('messenger.new_message') }}</a></li>
                  @each('frontend.messenger.partials.thread', $threads, 'thread', 'frontend.messenger.partials.no-threads')
                  {{--<li class="list-gradient"></li>--}}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- End Message Sidebar -->
      <div class="page-main">
        <div class="message-loading">
          <i class="fas fa-spinner fa-pulse"></i>
        </div>

        <!-- Chat Box -->
        <div class="messages-chats">
          <div class="chats" id="ajaxchat">

          </div>

        </div>
        <!-- End Chat Box -->

        <!-- Message Input-->
        {!! Form::open(array('url'=>'messages', 'id'=>'form-messageadd', 'role'=>'form','files' => true , 'parsley-validate'=>'','novalidate'=>' ', 'class'=>'messages-input')) !!}
          <input class="hidden" name="user_id" type="text" value="{{ encrypt(Auth::user()->id)  }}">
          <input class="hidden" name="thread_id" type="text">
          <div class="message-input">
            <textarea class="form-control" name="message" id="message" rows="1" placeholder="{{ trans('offers.general.enter_message') }}"></textarea>
          </div>
          <button class="message-input-btn btn btn-primary" type="button"><i class="fas fa-paper-plane"></i></button>
        {!! Form::close() !!}
        <!-- End Message Input-->

      </div>
    </div>
</div>

{{-- Loading bar for AJAX Loading --}}
<div class="load-progress"></div>
<div class="load-progress-animation"></div>

@include('frontend.messenger.partials.modal-message')

@stop

@section('after-scripts')
<script>
var threadId = {{ $threads->first()->id }};
var threadEl = $('#thread-' + threadId );
$(document).ready(function(){

  var threadLoading = $('.message-loading');
  loadThread(threadId);
  {{-- Refresh chat every 10 seconds --}}
  var refreshThread = setInterval(function(){ checkThread(threadId) }, 10000);

  {{-- Load Thread --}}
  function loadThread(threadId) {
    {{-- Load URL through AJAX --}}
    $.ajax({
      {{-- Set load progress bar width to 10% before load for smoother animation --}}
      beforeSend: function () {
        $('.load-progress-animation').removeClass('hide');
        $('.load-progress').css({
          width:'10%'
        });
        threadLoading.removeClass('hidden');
        threadEl.removeClass('active');
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
      url: '{{ url('messages/') }}/' + threadId,
      success: function(data) {
        {{-- Reset progress bar if XHR is not supported --}}
        $('.load-progress').css({
          width:  '100%'
        });
        $('.load-progress-animation').addClass('hide');
        $('.load-progress').css({
          width: '0%'
        });
        $('#ajaxchat').html(data);
        threadEl = $('#thread-' + threadId );
        scrollBottom();
        clearInterval(refreshThread);
        refreshThread = setInterval(function(){ checkThread(threadId) }, 10000);
        threadEl.addClass('active');
        threadLoading.addClass('hidden');
        $('input[name=thread_id]').val(threadId);
        {{-- Remove undread messages badge --}}
        if ($('#badge-' + threadId).length) {
          $('#badge-' + threadId).addClass('hidden');
        }
      }
    });
  }

  $('.list-group-item').click(function(e){
    loadThread(this.id.substring(7));
  });

  $('#message').keypress(function (e) {
    if (e.which == 13 && !($('#message').val().length === 0)) {
      $(".message-input-btn").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      sendMessage();
      return false;
    }
  });

  {{-- Send message --}}
  $(".message-input-btn").click(function(){
    if(!($('#message').val().length === 0)) {
      $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
      sendMessage();
    }
  });

  function sendMessage() {
    if(!($('#message').val().length === 0)) {
      $.ajax({
        url: $("#form-messageadd").attr("action") + '/' + $('input[name=thread_id]').val(),
        type: 'POST',
        data: $("#form-messageadd").serialize(),
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        success: function(data){
          threadId = data;
          loadThread(threadId);
          $(".message-input-btn").html('<i class="fas fa-paper-plane"></i>');
        },
        error: function(data){
          console.log(data.responseJSON.message);
          notie.alert('error', '<i class="fa fa-times m-r-5"></i>  ' +data.responseJSON.message,5)
          loadThread(threadId);
          $(".message-input-btn").html('<i class="fas fa-paper-plane"></i>');
        }
      })
      $('#message').val('');
    }
  }

  {{-- Load Thread --}}
  function checkThread(threadId) {
    $.ajax({
      url: '{{ url('messages') }}/' + threadId + '/check',
      success: function(data){
        if (data > 0) {
          loadThread(threadId);
        }
      }
    });
  }




  function scrollBottom() {
    var chats = $(".messages-chats");
    chats.scrollTop(chats[0].scrollHeight);
  }




  {{-- Refresh chat every 10 seconds
  var refreshId = setInterval(function() {
    $("#ajaxchat").load('{{ url('messages/') }}/' + threadId);
  }, 10000);--}}

  {{-- Refresh chat button --}}
  $("#refresh_chat").click(function(e){
    $("#ajaxchat").load('{{ url('messages/') }}/' + threadId, function() {
      $('#ajaxchat').animate({scrollTop: $('#ajaxchat').get(0).scrollHeight}, 1000);
    });
    e.preventDefault();
  });


  {{-- Open sidebar --}}
  $(".page-aside-switch").click(function(e){
    var sidebar = $(".page-aside");
    if (sidebar.hasClass('open')) {
      $(this).removeClass('open');
      sidebar.removeClass('open');
    } else {
      $(this).addClass('open');
      sidebar.addClass('open');
    }
  })


  });


</script>
@stop
