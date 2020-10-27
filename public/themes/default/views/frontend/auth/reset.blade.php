@extends(Theme::getLayout())

@section('subheader')
{{-- Gif Background --}}
<div class="page-login-gif-bg"></div>
{{-- Color overlay --}}
<div class="page-login-color-bg"></div>
@stop

@section('content')
  {{-- Start page login --}}
  <div class="page-login">
    <div class="vertical-align text-center">
      <div class="page-content vertical-align-middle">
        @if (session()->has('error'))
          <div class="panel border-radius bg-danger m-b-10 p-10">
            <i class="fa fa-times"></i> {!! session('error') !!}
          </div>
        @else
        @if (session()->has('success'))
          <div class="panel border-radius bg-success m-b-10 p-10">
            <i class="fa fa-check"></i> {!! session('success') !!}
          </div>
        @endif
        <div class="panel">
          <div class="game-bg"></div>
          <div class="panel-body padding-40">
            {{-- Logo --}}
            <div class="brand">
              <img src="{{ asset('img/logo.png') }}"
              title="Logo">
            </div>
            {{-- Top Text --}}
            <h3>{{ trans('auth.reset.reset_button') }}</h3>

            {{-- Login failed msg --}}
            <div class="bg-danger error" id="loginfailedFull">
              <i class="fa fa-times" aria-hidden="true"></i> {{ trans('auth.failed') }}
            </div>
            {{-- Start Reset password form --}}
            <form method="post" id="resetForm">
              <div class="bg-danger error reg" id="reset-errors-password">
              </div>
              {{-- Password input --}}
              <div class="input-group m-b-10" id="reset-password">
                <span class="input-group-addon login-form">
                  <i class="fa fa-unlock-alt" aria-hidden="true"></i>
                </span>
                <input id="password" type="password" class="form-control input" name="password" placeholder="{{ trans('auth.password') }}">
              </div>
              {{-- Password confirmation input --}}
              <div class="input-group m-b-10" id="reset-password-confirm">
                <span class="input-group-addon login-form">
                  <i class="fa fa-repeat" aria-hidden="true"></i>
                </span>
                <input id="password_confirmation" type="password" class="form-control input" name="password_confirmation" placeholder="{{ trans('auth.password_confirmation') }}">
              </div>
              {{-- Login button --}}
              <input type="hidden" name="token" value="{{ $token }}">
              <input type="hidden" name="email" value="{{ $email }}">
              <button type="submit" class="btn btn-success btn-block btn-animate btn-animate-vertical" id="resetPwSubmit">
                <span><i class="icon fa fa-unlock-alt" aria-hidden="true"></i> {{ trans('auth.reset.reset_button') }}</span>
              </button>
            </form>
            {{-- End Login form --}}
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
  {{-- End page login --}}

  @section('after-scripts')
    <script type="text/javascript">
    $(document).ready(function(){
      var resetForm = $("#resetForm");
      resetForm.submit(function(e){
        e.preventDefault();
        var formData = resetForm.serialize();
        $('#reset-errors-email').html( "" );
        $('#register-errors-password').html( "" );
        $('#reset-errors-email').slideUp('fast');
        $('#reset-errors-password').slideUp('fast');
        $('#reset-email').removeClass('has-error');
        $('#reset-password').removeClass('has-error');
        $('#reset-password-confirm').removeClass('has-error');

        $.ajax({
            url:'{{ url('password/reset') }}',
            type:'POST',
            data:formData,
            {{-- Send CSRF Token over ajax --}}
            headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
            beforeSend: function(){
              $("#resetPwSubmit").prop( "disabled", true );
              $("#resetPwSubmit").html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
            },
            success:function(data){
              window.location.href=data;
            },
            error: function (data) {
              console.log(data.responseText);
              var obj = jQuery.parseJSON( data.responseText );
              if(obj.email){
                $('#reset-email').addClass('has-error');
                $('#reset-errors-email').slideDown('fast');
                $('#reset-errors-email').html( obj.email );
              }
              if(obj.password){
                $('#reset-password').addClass('has-error');
                $('#reset-password-confirm').addClass('has-error');
                $('#reset-errors-password').slideDown('fast');
                $('#reset-errors-password').html( obj.password );
              }
              $("#resetPwSubmit").prop( "disabled", false );
              $("#resetPwSubmit").html('Login');
            }
        });
      });


    });
    </script>
  @endsection
@stop
