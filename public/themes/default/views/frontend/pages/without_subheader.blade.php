@extends(Theme::getLayout())

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
