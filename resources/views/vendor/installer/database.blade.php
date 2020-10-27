@extends('vendor.installer.layout')

@section('style')
    <style>
        .card-panel { display: none; }
    </style>
@endsection

@section('content')
    <div class="card grey darken-3">
         <form method="post" action="{{ url('install/database') }}">
            <div class="card-content white-text">
                <p class="card-title">{{ trans('installer.database.title') }}</p>
                <p style="margin-bottom: 40px;">{{ trans('installer.database.sub-title') }}</p>
                {!! csrf_field() !!}
                <div class="input-field">
                    <i class="material-icons prefix">settings</i>
                    <input type="text" id="dbname" name="dbname" value="{{ $database }}" required>
                    <label for="dbname">{{ trans('installer.database.dbname-label') }}</label>
                </div>
                <div class="input-field">
                    <i class="material-icons prefix">perm_identity</i>
                    <input type="text" id="username" name="username" value="{{ $username }}" required>
                    <label for="username">{{ trans('installer.database.username-label') }}</label>
                </div>
                <div class="input-field">
                    <i class="material-icons prefix">vpn_key</i>
                    <input type="text" id="password" name="password" value="{{ $password }}">
                    <label for="password">{{ trans('installer.database.password-label') }}</label>
                </div>
                <div class="input-field">
                    <i class="material-icons prefix">language</i>
                    <input type="text" id="host" name="host" value="{{ $host }}" required>
                    <label for="host">{{ trans('installer.database.host-label') }}</label>
                </div>
            </div>
            <div class="card-action">
                <button class="btn waves-effect waves-light light-green darken-3" type="submit">
                    {{ trans('installer.database.button') }}
                    <i class="material-icons right">send</i>
                </button>
            </div>
        </form>
    </div>
    <div class="card-panel light-green darken-3">
        <div class="card-content white-text">
            {{ trans('installer.database.wait') }}
            <br>
            <div class="progress light-green lighten-5">
                <div class="indeterminate light-green"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function(){
            $(document).on('submit', 'form', function(e) {
                $('.card').hide();
                $('.card-panel').show();
            });
        })
    </script>
@endsection
