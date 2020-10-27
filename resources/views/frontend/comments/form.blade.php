{{-- Comments wrapper --}}
<div id="comments-wrapper">
</div>

{{-- Start comments form --}}
<div class="panel">
  <div class="panel-heading">
    {{-- Post comment title --}}
    <h3 class="panel-title"><i class="fa fa-edit" aria-hidden="true"></i> {{ trans('comments.post_comment') }}</h3>
  </div>
  {{ Form::open(['id' => 'comment-submit']) }}
  <div class="p-20">
    {{-- Textarea --}}
    <textarea type="input" class="form-control input" id="commentText0" name="text" placeholder="{{ trans('comments.add_comment') }}" rows="3" data-parent="0"></textarea>
    {{-- Hidden inputs --}}
    <input type="input" name="item_id" value="{{$item_id}}" hidden>
    <input type="input" name="item_type" value="{{$item_type}}" hidden>
  </div>
  <div class="panel-footer">
    <div></div>
    <div>
      {{-- Post button --}}
      <a href="#" data-parent="0" class="button add-game disabled" id="commentSubmit">
        <i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}
      </a>
    </div>
  </div>
  {{ Form::close() }}
</div>
{{-- End comments form --}}

@section('comments-script')
<script type="text/javascript">
$(document).ready(function () {

  {{-- Load comments to comments wrapper --}}
  $( "#comments-wrapper" ).hide().load( "{{ url('comments/show/' . $item_type .'/' . $item_id)}}", function(){
    $("#comments_loading").fadeOut('slow');
  }).fadeIn('slow');

  {{--  Start submit new comment ajax --}}
  {{-- Check if user is logged in --}}
  @if(Auth::check())
  {{-- Submit comment --}}
  $("#commentSubmit").click(function(e) {
    e.preventDefault();
    if( $('#commentText0').val().length >= 0) {
      e.preventDefault();
      var formData =   $("#comment-submit").serialize();

      $.ajax({
        url: '{{ url("comments/new")}}',
        type: 'POST',
        data: formData,
        beforeSend: function(){
            $("#comments_loading").fadeIn('slow');
            $("#commentSubmit").prop( "disabled", true );
            $("#commentSubmit").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
        },
        success: function(data) {
          $('#commentText0').val("");
          $( "#comments-wrapper" ).load( data, function(){
            $("#comments_loading").fadeOut('slow');
          });
          $("#commentSubmit").prop( "disabled", false );
          $("#commentSubmit").html('<i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}');
          notie.alert('success', '<i class="fa fa-check m-r-5"></i> {{ trans('comments.alert.posted') }}',5)
        },
        error: function(data) {

          $("#comments_loading").fadeOut('slow');
          $("#commentSubmit").prop( "disabled", false );
          $("#commentSubmit").html('<i class="fa fa-comment" aria-hidden="true"></i> {{ trans('comments.post') }}');

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
    }

  });
  @else
  {{-- Open login modal if user is not logged in --}}
  $("#commentSubmit").click(function(e) {
    e.preventDefault();
    $('#LoginModal').modal('show');
  });
  @endif
  {{--  End submit new comment ajax --}}
});
</script>
@stop
