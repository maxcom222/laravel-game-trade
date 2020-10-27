@if(count($comments) > 0)

  {{-- Infos on top of comments --}}
  <div class="flex-center-space m-b-20">
    {{-- Total comments --}}
    <div class="total-comments">
      <i class="fa {{ $comments->total() == 1 ? 'fa-comment' : 'fa-comments' }}" aria-hidden="true"></i> {{ trans_choice('comments.comments_count', $comments->total(), ['count' => $comments->total()]) }}
    </div>
    <div>
      {{-- Post comment button --}}
      <a href="#" class="btn btn-dark post-comment"><i class="fa fa-edit" aria-hidden="true"></i> {{ trans('comments.post_comment') }}</a>
    </div>
  </div>

  {{-- Pagination on top --}}
  {{ $comments->links() }}

  @foreach($comments as $comment)
    {{-- Start comment --}}
    <div class="comment">
      {{-- User avatar --}}
      <div class="comment-left media-left">
        <a class="avatar avatar-lg @if($comment->user->isOnline()) avatar-online @else avatar-offline @endif" href="{{$comment->user->url}}">
          <img src="{{$comment->user->avatar_square_tiny}}" alt="{{$comment->user->name}} Avatar"><i></i>
        </a>
      </div>

      {{-- Comment content --}}
      <div class="media-body">
        {{-- Start comment --}}
        <div class="comment-body">
          {{-- Comment head (name, created, heart) --}}
          <div class="comment-head flex-center-space">
            {{-- User name + created at --}}
            <div>
              <a href="{{$comment->user->url}}" class="user-link"> {{$comment->user->name}} </a> <span class="created-at">{{$comment->created_at->diffForHumans()}}</span>
            </div>
            {{-- Heart icon --}}
            <div class="heart {{ \Auth::check() && $comment->dblikes->contains('user_id', \Auth::id()) ? 'liked' : '' }}" id="heart-head-{{$comment->id}}">
              <a href="{{ url('comments/likes/'.$comment->id) }}" class="pop">
                <i class="fa fa-heart"></i><span id="heart-head-count-{{$comment->id}}"> {{ $comment->likes ? $comment->likes : ''}}</span>
              </a>
            </div>
          </div>

          {{-- Comment content --}}
          <div class="comment-content">
            <p>{{$comment->content}}</p>
          </div>

          {{-- Comment actions (reply button) --}}
          <div class="comment-actions">
            {{-- Reply button --}}
            <a href="#" class="reply-comment btn btn-xs btn-dark m-r-5">
              <i class="fa fa-reply" aria-hidden="true"></i> {{ trans('comments.reply') }}
            </a>
            {{-- Heart icon --}}
            <a class="btn btn-xs @if($comment->dblikes->contains('user_id', \Auth::id())) btn-danger @else btn-dark @endif m-r-5" id="comment-like" href="{{$comment->id}}">
              <span id="icon"><i class="fa fa-heart"></i></span>&nbsp;<span id="countlike">{{$comment->likes}}</span>
            </a>
            {{-- Delete button --}}
            @can('edit_comments')
              <a class="btn btn-xs btn-dark delete-comment" href="{{ url('comments/delete/' . $comment->id. '/' . $comments->currentPage())}}">
                <i class="fa fa-trash" aria-hidden="true"></i> Remove
              </a>
            @endcan
          </div>
        </div>
        {{-- End comment --}}

        {{-- Start Replies --}}
        @if($comment->has_children)
          <div style="padding: 0px 20px;">
          {{-- Get all child comments --}}
          @php $childs = \App\Models\Comment::where('root_id', $comment->id)->with('user', 'dblikes')->get() @endphp
          @foreach($childs as $child)
            <div class="reply m-b-20">
              <div class="reply-head flex-center-space">
                {{-- Child comment user --}}
                <div>
                  <a href="{{$child->user->url}}" class="user-link">
                    <span class="avatar avatar-xs @if($child->user->isOnline()) avatar-online @else avatar-offline @endif">
                      <img src="{{$child->user->avatar_square_tiny}}" alt="{{$child->user->name}}'s Avatar"><i></i>
                    </span>
                    {{$child->user->name}}
                  </a>
                  <span class="created-at">{{$child->created_at->diffForHumans()}}</span>
                </div>
                {{-- Heart icon --}}
                <div class="heart {{ \Auth::check() && $child->dblikes->contains('user_id', \Auth::id()) ? 'liked' : '' }}" id="heart-head-{{$child->id}}">
                  <a href="{{ url('comments/likes/'.$child->id) }}" class="pop">
                    <i class="fa fa-heart"></i><span id="heart-head-count-{{$child->id}}">{{ $child->likes ? $child->likes : ''}}</span>
                  </a>
                </div>
              </div>
              {{-- Child comment body --}}
              <div class="comment-body" style="padding: 0px 10px 10px 10px;">
                {{-- Child comment content --}}
                <div class="comment-content">
                  <p>{{$child->content}}</p>
                </div>
                <div class="comment-actions">
                  {{-- Heart icon --}}
                  <a class="btn btn-xs @if($child->dblikes->contains('user_id', \Auth::id())) btn-danger @else btn-dark @endif m-r-5" id="comment-like" href="{{$child->id}}">
                    <span id="icon"><i class="fa fa-heart"></i></span>&nbsp;<span id="countlike">{{$child->likes}}</span>
                  </a>
                  {{-- Delete button --}}
                  @can('edit_comments')
                    <a class="btn btn-xs btn-dark delete-comment" href="{{ url('comments/delete/' . $child->id . '/' . $comments->currentPage())}}">
                      <i class="fa fa-trash" aria-hidden="true"></i> Remove
                    </a>
                  @endcan
                </div>
              </div>
            </div>
          @endforeach
          </div>
        @endif
        {{-- End Replies --}}

        {{-- Start reply form --}}
        <div class="reply-form-wrapper">
          <div class="reply-form">
            {{ Form::open(['class' => 'reply-form','data-root' => $comment->id]) }}
              {{-- Reply form input --}}
              <textarea type="input" class="form-control input" name="replyText" placeholder="{{ trans('comments.add_comment') }}" rows="2" data-parent="0"></textarea>
              {{-- Hidden inputs --}}
              <input type="input" name="parent_id" value="{{$comment->id}}" hidden>
              <input type="input" name="current_page" value="{{$comments->currentPage()}}" hidden>
              {{-- Reply submit button --}}
              <button data-parent="0" class="btn btn-success m-t-10" id="reply-submit-{{$comment->id}}"><i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}</button>
            {{ Form::close() }}
          </div>
        </div>
        {{-- End reply form --}}
      </div>
    </div>
    {{-- End comment --}}
  @endforeach

  {{-- Pagination on bottom --}}
  {{ $comments->links() }}


  <script type="text/javascript">

    {{-- Load likes in popover ---}}
    $('.pop').popover({
        html: true,
        placement: 'bottom',
        trigger: 'focus',
        template: '<div class="popover comment-likes"><div class="popover-arrow"></div><h3 class="popover-title" role="tooltip"></h3><div class="popover-content"></div></div>',
        content: function(){
            var content_id = "content-id-" + $.now();
            {{-- Get likes ajax --}}
            $.ajax({
                type: 'GET',
                url: $(this).prop('href'),
                cache: false,
            }).done(function(d){
                $('#' + content_id).html(d);
            });

            {{-- Return spinner before loading --}}
            return '<div id="' + content_id + '"><div class="text-center"><i class="fa fa-lg fa-spinner fa-spin fa-fw"></i></div></div>';
        }
    }).click(function(e) { e.preventDefault() });

    @can('edit_comments')
      $(".delete-comment").click(function(e) {
        e.preventDefault();
        $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
        $.ajax({
            type: 'GET',
            url: $(this).prop('href'),
            cache: false,
        }).done(function(data){
          $( "#comments-wrapper" ).load( data, function(){
            $("#comments_loading").fadeOut('slow');
          });
          notie.alert('success', '<i class="fa fa-check m-r-5"></i> Comment deleted!',5)
        });
      });
    @endcan


    $(function() {
      {{-- Ajax Pagination --}}
      $(".pagination a").click(function(e) {
        e.preventDefault();
        $(this).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
        $("#comments_loading").fadeIn('fast');
        var url = $(this).attr('href');
        $( "#comments-wrapper" ).load( url, function() {
          $("#comments_loading").fadeOut('fast');

          $('html, body').animate({
              scrollTop: $("#comments-wrapper").position().top
          }, 200);

        });
      });

      {{-- Like comment --}}
      @if(Auth::check())
        $('[id=comment-like]').click(function(e) {
          e.preventDefault();
          var id = $(this).attr('href');
          var element = $(this);

            {{-- Post like --}}
            $.ajax({
              url: '{{ url("comments/like")}}',
              type: 'POST',
              data: { id: id},
              {{-- Send CSRF Token over ajax --}}
              headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
              beforeSend: function(){
                element.find('#icon').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
              },
              success: function(data) {
                if(element.find('#countlike').text() < data){
                    element.addClass("btn-danger");
                    element.removeClass("btn-dark");
                    $('#heart-head-'+id).addClass('liked');
                    $('#heart-head-count-'+id).html(' '+data);
                }else{
                    element.addClass("btn-dark");
                    element.removeClass("btn-danger");
                    $('#heart-head-'+id).removeClass('liked');
                    if(data == 0){
                      $('#heart-head-count-'+id).html('');
                    }else{
                      $('#heart-head-count-'+id).html(' '+data);
                    }
                }
                element.find('#countlike').html(data);
                element.find('#icon').html('<i class="fa fa-heart"></i>');

              }
            });

        });
      @else
        $("[id=comment-like]").click(function(e) {
          e.preventDefault();
          $('#LoginModal').modal('show');
        });
      @endif


      {{--  Start submit reply comment ajax --}}
      $('#commentText0').on("keyup", action);

      function action() {
          if( $('#commentText0').val().length > 0) {
              $('#commentSubmit').prop("disabled", false);
          } else {
              $('#commentSubmit').prop("disabled", true);
          }
      };

      @if(Auth::check())
      $(".reply-form").submit(function(e) {
        e.preventDefault();
        var formData =   $(this).serialize();
        var element = $(this);
        var rootId = $(this).data('root');
        var submitButton =  $('#reply-submit-'+rootId);

        $.ajax({
          url: '{{ url("comments/new/reply")}}',
          type: 'POST',
          data: formData,
          beforeSend: function(){
            submitButton.html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            submitButton.prop( "disabled", true );
            $("#comments_loading").fadeIn('slow');
          },
          success: function(data) {
            console.log(data);
            submitButton.prop( "disabled", false );
            submitButton.html('<i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}');
            $( "#comments-wrapper" ).load( data, function(){
              $("#comments_loading").fadeOut('slow');
            });
            notie.alert('success', '<i class="fa fa-check m-r-5"></i> {{ trans('comments.alert.reply_posted') }}',5)
          },
          error: function(data) {
            submitButton.prop( "disabled", false );
            submitButton.html('<i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}');
            if(data.responseJSON.error == 'no_input') {
              notie.alert('error', '<i class="fa fa-times m-r-5"></i> {{ trans('comments.alert.no_input') }}',5)
            }

            if(data.responseJSON.error == 'throttle') {
              notie.alert('error', '<i class="fa fa-times m-r-5"></i> {{ trans('comments.alert.throttle') }}',5)
            }

            if(data.responseJSON.error == 'login') {
              $('#LoginModal').modal('show');
            }
          }
        });

      });

      $(".reply-comment").click(function(e) {
        e.preventDefault();
        var current = $(this).parent().parent().parent().find('.reply-form-wrapper');
        $('.reply-form-wrapper').not(current).slideUp();
        current.slideToggle();
        $('html, body').animate({
            scrollTop: current.offset().top - 300
        }, 200);
      });


      @else
      {{-- Open login modal if user is not logged in --}}
      $(".reply-comment").click(function(e) {
        e.preventDefault();
        $('#LoginModal').modal('show');
      });
      @endif

      $('.post-comment').click(function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $(document).height() - 500
        }, 300);
      });

    });
  </script>
@endif
