<?php  if( isset($_GET["n"]) && isset($_GET["c"]) ) { ?>


@php

$client = new \GuzzleHttp\Client();

$res = $client->request('GET', url('metacritic/search/game?platform=' . $_GET["c"] . '&title=' . $_GET["n"]));
            // JSON Request


$json_results = json_decode($res->getBody())->results;


@endphp

@if(count($json_results) == 0)

<section class="panel">

  <div class="panel-body" style="text-align: center;">

    <i class="fa fa-frown-o" aria-hidden="true" style="font-size: 100px;"></i>
    <div>Sorry, no search results found for <strong>{{$_GET["n"]}}</strong>.</div>

  </div>

</div>

@endif


@php
foreach($json_results as $result) {

  // Check is game have release date
  if(isset($result->rlsdate)) {
    $release = $result->rlsdate;
    setlocale(LC_TIME, 'de_DE', 'de_DE.UTF-8');
    $release_date = strftime('%d. %B %Y',strtotime($release));
  }else{
    $release = 0;
  }

  // Check and get platform data
  $platform = DB::table('platforms')->where('acronym', $_GET["c"])->first();

  if($platform) {
    $platform_name = $platform->name;
    $platform_color = $platform->color;
    $platform_id = $platform->id;
    $platform_meta = $platform->acronym;
  }else{
    $platform_name = $result->platform;
    $platform_color = "#e8eff0";
    $platform_id = 0;
    $platform_meta = 0;
  }

  // Check and get game data if exist

  $game = DB::table('games')->where('platform_id', $platform_id)->where('release_date', $release)->where('name', $result->name)->first();

@endphp



<section class="panel">

  <div class="panel-body">
    <div style="display: flex; align-items: center;">
      <div style="margin-right: 20px;">
        <span class="avatar avatar-lg">
          @if($game)
          <img src="{{ $game->cover_tiny_square }}" alt="{{ $game->name }}">
          @else
          <img src="{{ asset('uploads/users/no_avatar.jpg') }}" alt="Nicht in der Datenbank">
          @endif
        </span>
      </div>
      <div>
        <div style="font-weight: 500; color: #fff; font-size: 20px; margin-bottom: 5px; margin-top: -5px;">{{ $result->name }}</div>
        <span style="background-color:{{ $platform->color }}; margin-right:6px; padding: 3px 7px; border-radius: 5px; color: #fff;">{{ $platform_name }} </span>
        <span style="color: rgba(255,255,255,0.5);"><i class="fa fa-calendar"></i> {{ $release_date }} </span>
      </div>
    </div>
  </div>

  <div class="panel-footer">
    <div style="padding-left: 30px;">
      In the Database @if($game)<i class="fa fa-check-circle text-success" aria-hidden="true"></i>@else <i class="fa fa-times-circle text-danger" aria-hidden="true"></i>@endif
    </div>
    @if($game)
    <a href="{{ url('games/' . $game->id) }}" class="button">
      <i class="fa fa-arrow-right" aria-hidden="true"></i> Details
    </a>
    @else
    <a href="{{ url('games/add/' . $platform_meta . '+' . $result->name ) }}" class="button add-game game_add">
      <i class="fa fa-plus" aria-hidden="true"></i> Add Game
    </a>
    @endif
  </div>

</section>



<?php


            }
        }


?>

{{-- Open loading modal on game add --}}
<script type="text/javascript">
$(document).ready(function(){


    $(".game_add").click(function(event){
      var href= $(this).attr('href');

      $('#modal_game_add').modal('show');
      setTimeout(function(){
        window.location=href;
       },500) // 3 seconds.

      // override browser following link when clicked
      return false;
    });

})
</script>
