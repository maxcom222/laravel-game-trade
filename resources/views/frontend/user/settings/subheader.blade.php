@section('subheader')

  <div class="subheader tabs">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>
    {{-- Settings title --}}
    <div class="content">
      <span class="title"><i class="fa fa-wrench"></i> {{ trans('users.dash.settings.settings') }}</span>
    </div>

    <div class="tabs">
      {{-- Profile tab --}}
      <a class="tab {{  Request::is('dash/settings') ? 'active' : ''}}" href="{{url('dash/settings')}}">
        {{ trans('users.dash.settings.profile') }}
      </a>
      {{-- Password tab --}}
      <a class="tab {{  Request::is('dash/settings/password') ? 'active' : ''}}" href="{{url('dash/settings/password')}}">
        {{ trans('users.dash.settings.password') }}
      </a>
    </div>

  </div>

@stop
