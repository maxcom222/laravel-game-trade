@extends(Theme::getLayout())

@section('subheader')

<div style="position: relative">


  <div class="page-top-background" style="position: absolute; z-index:0 !important; top: 0; width: 100%;">
    <div class="background-overlay listings-overview"></div>
  </div>

</div>

@stop

@section('content')

<div class="content-title m-b-20"><i class="fa fa-search" aria-hidden="true"></i> {{ trans('games.overview.search_result', ['value' => $value]) }}</div>

{{-- <hr style="border-top: 1px solid rgba(255,255,255,0.2)"> --}}


  {{-- START GAME LIST --}}
  <div class="row">
    @forelse($games as $game)
      {{-- START GAME --}}
      <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 col-xl-2 m-b-20">
        {{-- Start Game Cover --}}
        <div class="card game-cover-wrapper hvr-grow-shadow" style="margin-bottom: 5px;">
          {{-- Show "New!" label if item or price is not older than 1 day --}}
          @if(Carbon\Carbon::now()->subDays(1) < $game->created_at )
            <div class="item-new">{{ trans('listings.general.new') }}</div>
          @endif
          {{-- Pacman Loader for background image - show only when cover exists --}}
          @if($game->image_cover)
          {{-- <div class="loader pacman-loader cover-loader"></div> --}}
          {{-- Show game name, when no cover exist --}}
          @else
          <div class="no-cover-name">{{$game->name}}</div>
          @endif

          <a href="{{ $game->url_slug }}">

            {{-- Generated game cover with platform on top --}}
            @if($game->cover_generator)
              <div class="lazy game-cover gen"  data-original="{{$game->image_cover}}"></div>
              <div class="game-platform-gen" style="background-color: {{$game->platform->color}}; text-align: {{$game->platform->cover_position}};">
                {{-- Check if platform logo setting is enabled --}}
                @if( config('settings.platform_logo') )
                  <img src="{{ asset('logos/' . $game->platform->acronym . '_tiny.png/') }}" alt="{{$game->platform->name}} Logo">
                @else
                  <span>{{$game->platform->name}}</span>
                @endif
              </div>
            {{-- Normal game cover --}}
            @else
              <div class="lazy game-cover"  data-original="{{$game->image_cover}}"></div>
            @endif
            {{-- Item name --}}
            @if($game->image_cover)
            <div class="item-name">
              {{ $game->name }}
            </div>
            @endif
          </a>
        </div>
        {{-- End Game Cover --}}

        @if($game->listingsCount > 0 || isset($game->metacritic))
        <div class="listing-details flex-center-space">
          @if($game->listingsCount > 0)
          <div class="listing-active">
            <i class="fa {{ $game->listingsCount == 1 ? 'fa-tag' : 'fa-tags' }}"></i> {{ $game->listingsCount }}
          </div>
          @else
          <div></div>
          @endif
          @if(isset($game->metacritic) && $game->metacritic->score)
          <div class="listing-active {{$game->metacritic->score_class}}">
            {{ $game->metacritic->score }}
          </div>
          @endif
        </div>
        @endif

      </div>
    {{-- End GAME --}}
    @empty
      {{-- Start empty list message --}}
      <div class="empty-list">
        {{-- Icon --}}
        <div class="icon">
          <i class="far fa-frown" aria-hidden="true"></i>
        </div>
        {{-- Text --}}
        <div class="text">
          {{ trans('games.overview.no_search_result', ['value' => $value]) }}
        </div>
      </div>
      {{-- End empty list message --}}
    @endforelse

  </div>
  {{-- END GAME LIST --}}

  {{ $games->links() }}

@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('search', $value) !!}
@endsection
{{-- End Breadcrumbs --}}
