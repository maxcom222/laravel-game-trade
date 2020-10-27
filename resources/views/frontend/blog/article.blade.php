@extends(Theme::getLayout())

@section('subheader')
  <div class="subheader">

    <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}') !important;"></div>
    <div class="background-color"></div>

    <div class="content">
      <span class="title"><i class="fa fa-newspaper" aria-hidden="true"></i> {{ trans('general.blog') }}</span>
    </div>

  </div>

@endsection

@section('content')

  <div class="panel">
    <div class="article-picture" style="background-image: url('{{$article->image_large}}') !important;">

    </div>
    <div class="article">
      {{-- Article head --}}
      <div class="panel-heading p-20">
        @if(isset($article->category))<span class="article-category">{{$article->category->name}}</span>@endif
        <span class="article-title block">{{ $article->title }}</span>
      </div>
      {{-- Article body --}}
      <div class="panel-body p-20">
        {!! $article->content !!}
      </div>
      {{-- Article footer --}}
      <div class="panel-footer p-20">
        <div class="article-footer">
        {{$article->created_at->diffForHumans()}}
        </div>
      </div>
    </div>
  </div>

    {{-- Load comments --}}
    @if(config('settings.comment_article'))
      @php $item_type = 'article'; $item_id = $article->id; @endphp
      @include('frontend.comments.form')
    @endif

    {{-- Start Breadcrumbs --}}
    @section('breadcrumbs')
    {!! Breadcrumbs::render('article', $article) !!}
    @endsection
    {{-- End Breadcrumbs --}}

@section('after-scripts')
  {{-- Load comment script --}}
  @if(config('settings.comment_article'))
    @yield('comments-script')
  @endif
@endsection

@stop
