{{-- begin: Footer --}}
<footer class="bp-footer d-flex align-items-center justify-content-between">
    <!-- To the right -->
    <div class="bp-footer-user-panel bp-bg-light rounded-lg d-flex align-items-center">

        <a class="bp-footer-frontend" href="{{ url('/') }}">
            <i class="far fa-window"></i>{{ trans('admin.frontend') }}
        </a>

        <div class="bp-footer-user border-right border-left">
            <img alt="{{ Backport::user()->name }}" src="{{ Backport::user()->avatar_square_tiny }}"><strong>{{ Backport::user()->name }}</strong>
        </div>

        <a class="bp-footer-logout" href="{{ url('logout') }}">
            <i class="fas fa-sign-out-alt"></i>{{ trans('admin.logout') }}
        </a>
    </div>
    <!-- Default to the left -->

    <div class="d-flex align-items-center">
        @if(config('backport.show_version'))
            <img alt="Backport Logo" src="{{ asset('vendor/backport/media/logos/logo-footer.png') }}" />&nbsp;&nbsp;<strong class="text-muted">{!! \Wiledia\Backport\Backport::VERSION !!}</strong>
        @endif
        @if(config('backport.show_environment'))
            <span class="ml-2">{!! env('APP_ENV') !!}</span>
        @endif
    </div>
</footer>
{{-- end: Footer --}}
