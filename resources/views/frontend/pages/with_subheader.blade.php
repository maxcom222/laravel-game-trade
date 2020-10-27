@extends(Theme::getLayout())

@section('subheader')
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    <div class="content">
      <span class="title"><i class="fa {{ $page->extras['subheader_icon'] }}"></i> {{ $page->extras['subheader_title'] }}</span>
    </div>

  </div>

@endsection

@section('content')
  <div class="panel">
    <div class="panel-body">
      {!! $page->content !!}
    </div>
  </div>
@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('page', $page) !!}
@endsection
{{-- End Breadcrumbs --}}
