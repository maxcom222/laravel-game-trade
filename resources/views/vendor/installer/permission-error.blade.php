@extends('vendor.installer.layout')

@section('content')
    <div class="card red darken-3 hoverable">
        <div class="card-content white-text">
            <p class="card-title" style="margin-bottom: 20px;"> <i class="small material-icons">info_outline</i> {{ trans('installer.permission-error.title') }}</p>
            <p>{{ trans('installer.permission-error.sub-title') }} <strong> {{ $permissionCheck . '.'}} </strong></p>
        </div>
        <div class="card-action white-text" style="opacity: 0.5;">
          {{ trans('installer.permission-error.message')}}
        </div>
    </div>
@endsection
