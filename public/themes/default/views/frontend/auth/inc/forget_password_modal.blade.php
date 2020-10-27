{{-- Start Forget Password Modal --}}
<div class="modal fade modal-fade-in-scale-up modal-dark" id="ForgetModal" tabindex="-1" role="dialog">
  <div class="modal-dialog user-dialog modal-sm" role="document">
    <div class="modal-content">

      <div class="modal-header" >
        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>
        <div class="title">
          {{-- Sign in button --}}
          <a data-dismiss="modal" data-toggle="modal" href="#LoginModal" class="btn btn-success btn-round f-w-500 float-right"><i class="fa fa-sign-in" aria-hidden="true"></i></a>
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-unlock" aria-hidden="true"></i>
            <strong> {{ trans('auth.password_forgot') }}</strong>
          </h4>
        </div>
      </div>

      <div class="modal-body user-body">
        <div class="row no-space">
          <div class="col-md-12 form" id="loginform">
            <div class="bg-success error reg" id="forget-success">
              <i class="fa fa-check"></i> {{ trans('auth.reset.sent') }}
            </div>
            <form id="forgetForm" method="POST" novalidate="novalidate">
              {{ csrf_field() }}
              <div class="bg-danger error reg" id="forget-errors-email">
              </div>
              {{-- eMail Adress input --}}
              <div class="input-group m-b-10">
                <span class="input-group-addon login-form">
                  <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
                <input id="forget-email" type="email" class="form-control input rounded" name="email" value="{{ old('email') }}" placeholder="{{ trans('auth.email') }}">
              </div>
              {{-- Forget password submit button --}}
              <button type="submit" class="btn btn-dark btn-block btn-animate btn-animate-vertical" id="forget">
                <span><i class="icon fa fa-unlock" aria-hidden="true"></i> {{ trans('auth.reset.reset_button') }}</span>
              </button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
{{-- End Forget Password Modal --}}
