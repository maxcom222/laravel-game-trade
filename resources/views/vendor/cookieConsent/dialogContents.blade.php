<div class="cookie-consent flex-center hide" id="js-cookie-consent">

    <img src="{{ asset('img/cookie.png') }}" />
    <div class="inline-block m-l-10">
        <span class="cookie-consent__message m-b-10">
            {!! trans('general.cookie.message') !!}
        </span>

        <button class="btn btn-round js-cookie-consent-agree cookie-consent__agree">
            <i class="fas fa-cookie-bite"></i> {{ trans('general.cookie.agree') }}
        </button>
    </div>

</div>
