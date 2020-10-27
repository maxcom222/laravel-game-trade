@extends('backpack::layout')

@section('after_scripts')
    <!-- jQuery and jQuery UI (REQUIRED) -->
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    <!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> -->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/elfinder.min.css') ?>">
    <!-- <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/theme.css') ?>"> -->
    <link rel="stylesheet" type="text/css" href="<?= asset('vendor/backpack/elfinder/material.theme.css') ?>">

    <!-- elFinder JS (REQUIRED) -->
    <script src="<?= asset($dir.'/js/elfinder.min.js') ?>"></script>

    <?php if($locale){ ?>
    <!-- elFinder translation (OPTIONAL) -->
    <script src="<?= asset($dir."/js/i18n/elfinder.$locale.js") ?>"></script>
    <?php } ?>

    <!-- elFinder initialization (REQUIRED) -->
    <script type="text/javascript" charset="utf-8">
        // Documentation for client options:
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        $().ready(function() {
            $('#elfinder').elfinder({
                // set your elFinder options here
                <?php if($locale){ ?>
                    lang: '<?= $locale ?>', // locale
                <?php } ?>
                customData: {
                    _token: '<?= csrf_token() ?>'
                },
                height: window.innerHeight,
                url : '<?= route("elfinder.connector") ?>'  // connector URL
            });

            {{-- Change height of elFinder on window resize --}}
            $(window).resize(function(){
                var h = ($(window).height());
                if($('#elfinder').height() != h){
                    $('#elfinder').height(h).resize();
                }
            });


        });
    </script>
@endsection

@section('header')
  <!-- Element where elFinder will be created (REQUIRED) -->
  <div id="elfinder"></div>

  <!-- elFinder full height content fix -->
  <style> .content { min-height: 0px !important; padding: 0 !important; } </style>
@endsection

@section('content')


@endsection
