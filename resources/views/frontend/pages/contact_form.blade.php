@extends(Theme::getLayout())

@section('subheader')
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    <div class="content">
        <span class="title"><i class="fa {{ $page->extras['subheader_icon'] }}"></i> {{ $page->extras['subheader_title'] }}</span>
    </div>

  </div>

@endsection

@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="panel">
      <div class="panel-body">
        {!! $page->content !!}
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel">
      <form id="contact-form" action="{{ url('contact') }}" method="POST" novalidate="novalidate">
        {{ csrf_field() }}
        <div class="panel-body">
          @if($errors->has('name'))
            {{-- Name error msg --}}
            <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
              <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('name') }}
            </div>
          @endif
          <div class="input-wrapper">
            {{-- Name input --}}
            <div class="input-group {{$errors->has('name') ? 'has-error' : '' }}">
              <span class="input-group-addon fixed-width">
                <i class="fa fa-user" aria-hidden="true"></i>
              </span>
              {{ Form::input('name', 'name', null, ['class' => 'form-control rounded inline input', 'placeholder' => trans('general.contact.name')]) }}
            </div>
          </div>
          @if($errors->has('email'))
            {{-- Email error msg --}}
            <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
              <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('email') }}
            </div>
          @endif
          <div class="input-wrapper">
            {{-- Mail input --}}
            <div class="input-group {{$errors->has('email') ? 'has-error' : '' }}">
              <span class="input-group-addon fixed-width">
                <i class="fa fa-envelope" aria-hidden="true"></i>
              </span>
              {{ Form::input('email', 'email', null, ['class' => 'form-control rounded inline input', 'placeholder' => trans('general.contact.email')]) }}
            </div>
          </div>
          @if($errors->has('message'))
            {{-- Message error msg --}}
            <div class="bg-danger m-b-10 b-r p-10" id="loginfailedFull">
              <i class="fa fa-times" aria-hidden="true"></i> {{ $errors->first('message') }}
            </div>
          @endif
          <div class="input-wrapper {{$errors->has('message') ? 'has-error' : '' }}">
            {{-- Text input --}}
            {{ Form::textarea('message', null, ['class' => 'form-control rounded inline input', 'placeholder' => trans('general.contact.message'),'rows' => '5']) }}
          </div>
        </div>
        <div class="panel-footer">
          <div></div>
          <div>
            <a href="javascript:void(0)" class="button add-game" id="send-message">
              <i class="fa fa-paper-plane" aria-hidden="true"></i> {{ trans('general.contact.send') }}
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('page', $page) !!}
@endsection
{{-- End Breadcrumbs --}}

@section('after-scripts')
  <script type="text/javascript">
  $(document).ready(function(){
    {{-- Contact submit --}}
    $("#send-message").click( function(){
      $('#send-message').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
      $('#send-message').addClass('loading');
      $('#contact-form').submit();
    });
  });
  </script>
@stop
