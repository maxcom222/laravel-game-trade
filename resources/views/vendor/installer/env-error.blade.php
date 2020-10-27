@extends('vendor.installer.layout')

@section('content')
    <div class="card red darken-3 hoverable">
        <div class="card-content white-text">
            <p class="card-title" style="margin-bottom: 20px;"> <i class="small material-icons">info_outline</i> {{ trans('installer.permission-error.title') }}</p>
            <p>@if(!$env && !$app){{ trans('installer.permission-error.env-app-sub-title') }} @elseif(!$app) {{ trans('installer.permission-error.app-sub-title') }} @else {{ trans('installer.permission-error.env-sub-title') }} @endif</p>
        </div>
        <div class="card-action white-text">
          <a class="btn waves-effect waves-light grey darken-3" href="{{ url('install/database') }}">
              {{ trans('installer.database-error.button') }}
          </a>
        </div>
    </div>
@endsection
