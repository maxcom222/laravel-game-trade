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
  @foreach($articles as $article)
    <div class="panel">
      <div class="article-picture" style="background-image: url('{{$article->image_large}}') !important;">

      </div>
      <div class="article">
        {{-- Article head --}}
        <div class="panel-heading p-20">
          @if(isset($article->category))<span class="article-category">{{$article->category->name}}</span>@endif
          <span class="article-title block"><a href="{{$article->url_slug}}">{{ $article->title }}</a></span>
        </div>
        {{-- Article body --}}
        <div class="panel-body article-body-limit p-20">
          {!! mb_strimwidth(preg_replace('/<[\/\!]*?[^<>]*?>/si', '', $article->content), 0, 800, '...') !!}
        </div>
        {{-- Article footer --}}
        <div class="panel-footer">
          <div class="article-footer p-20">
            {{$article->created_at->diffForHumans()}}
          </div>
        </div>
      </div>
    </div>
  @endforeach
@stop

{{-- Start Breadcrumbs --}}
@section('breadcrumbs')
{!! Breadcrumbs::render('blog') !!}
@endsection
{{-- End Breadcrumbs --}}
