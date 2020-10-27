@extends('vendor.installer.layout')

@section('content')
    <div class="card grey darken-3">
        <div class="card-content white-text">
            <div class="center-align">
                <p class="card-title"><img src="{{ asset('img/logo@2x.png')}}"></p>
                <p style="opacity:0.5;">{{ trans('installer.welcome.version') }}</p>
            </div>
            <p class="card-title">{{ trans('installer.welcome.title') }}</p>
            <p>{{ trans('installer.welcome.sub-title') }}</p>
            <ol>
                @for ($i = 1; $i < 5; $i++)
                    <li>{{ trans('installer.welcome.item' . $i) }}</li>
                @endfor
            </ol>
            <p>{{ trans('installer.welcome.message') }}</p>
            <div class="white-text red" style="padding: 5px; margin-top: 20px; display: flex; align-items: center;">
               <div style="margin-right: 10px;"><i class="medium material-icons">error_outline</i></div>
               <div>{{ trans('installer.welcome.info') }}</div>
            </div>
        </div>
        <div class="card-action">
            <a class="btn waves-effect waves-light light-green darken-3" href="{{ url('install/database') }}">
                {{ trans('installer.welcome.button') }}
                <i class="material-icons right">send</i>
            </a>
        </div>
    </div>
@endsection
