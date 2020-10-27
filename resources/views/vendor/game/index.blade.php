@extends('layouts.app')

@section('content')

  {{-- START GAME LIST --}}
  <div class="row">

    {{-- START GAME --}}
    <div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
      <div class="card card-game card-dark" style="border-radius: 5px; background-color: rgba(0,0,0,0);">
        <div class="card-img">

          <!-- GAME PIC -->
          <a href="#">
            <div class="image-wrapper image-wrapper--loading">
              <img class="lazy overlay-figure overlay-scale no-cover" src="https://www.gamabo.at/uploads/cover/1478869581-21143645_cover_home_small.jpg" alt="asd Cover" style="border-radius: 5px;">
            </div>
          </a>

        </div>
      </div>
    </div>

  </div>
  {{-- END GAME LIST --}}

@stop
