@extends(Theme::getLayout())

{{-- Add Settings Subheader --}}
@include('frontend.user.settings.subheader')

@section('content')

    <section class="panel">
      {{-- Panel heading (Change password) --}}
      <div class="panel-heading">
        <h3 class="panel-title">{{ trans('users.dash.settings.password_heading') }}</h3>
      </div>
      {{-- Open Form for password --}}
      {!! Form::open(array('url'=>'dash/settings/password','id'=>'form-password')) !!}
      <div class="panel-body">
        <div class="input-wrapper">
          {{-- Old password label --}}
          <label>{{ trans('users.dash.settings.password_old') }}</label>
          {{-- Error messages for old input --}}
          @if($errors->has('old_password'))
          <div class="bg-danger input-error">
            @foreach($errors->get('old_password') as $message)
            {{$message}}
            @endforeach
          </div>
          @endif
          {{-- Input for old password --}}
          <div class="input-group {{$errors->has('old_password') ? 'has-error' : '' }}">
            <span class="input-group-addon fixed-width">
              <i class="fa fa-key" aria-hidden="true"></i>
            </span>
            <input id="password" type="password" class="form-control input" name="old_password" placeholder="{{ trans('users.dash.settings.password_old') }}" value="{{ old('old_password') }}">
          </div>
        </div>
        <div class="input-wrapper">
          {{-- New password label --}}
          <label>{{ trans('users.dash.settings.password_new') }}</label>
          {{-- Error messages for new input --}}
          @if($errors->has('password'))
          <div class="bg-danger input-error">
            @foreach($errors->get('password') as $message)
            {{$message}}
            @endforeach
          </div>
          @endif
          {{-- Input for new password --}}
          <div class="m-b-10 input-group {{$errors->has('password') ? 'has-error' : '' }}">
            <span class="input-group-addon fixed-width">
              <i class="fa fa-unlock-alt" aria-hidden="true"></i>
            </span>
            <input id="password" type="password" class="form-control input" name="password" placeholder="{{ trans('users.dash.settings.password_new') }}" value="{{ old('password') }}">
          </div>
          {{-- Input for new paddword confirmation --}}
          <div class="input-group {{$errors->has('password') ? 'has-error' : '' }}">
            <span class="input-group-addon fixed-width">
              <i class="fa fa-repeat" aria-hidden="true"></i>
            </span>
            <input id="password" type="password" class="form-control input" name="password_confirmation" placeholder="{{ trans('users.dash.settings.password_new_confirm') }}" value="{{ old('password_confirmation') }}">
          </div>
        </div>
      </div>

      <div class="panel-footer">
        <div>
        </div>
        <div>
          {{-- Save button --}}
          <a href="javascript:void(0)" class="button" id="password-submit">
            <i class="fa fa-save" aria-hidden="true"></i> {{ trans('general.save') }}
          </a>
        </div>
      </div>
      {!! Form::close() !!}
      {{-- Close Form for password --}}

    </section>

@stop


@section('after-scripts')
<script type="text/javascript">
$(document).ready(function(){

  {{-- password submit --}}
  $("#password-submit").click( function(){
    $('#password-submit').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
    $('#password-submit').addClass('loading');
    $('#form-password').submit();
  });

})
</script>
@stop
