<div class="site-footer">
  <div class="flex-center-space">
    <div class="footer-menu-wrapper">
      @php $last_parent = false; @endphp
      @foreach($menu as $item)

        {{-- First iteration - close menu --}}
        @if($loop->first && !$item->children->count()>0)
          <div class="m-r-20 m-b-20">
            <ul class="footer-menu">
        @endif

        {{-- Check if menu item is parent item --}}
        @if($item->children->count()>0)
          @if(!$loop->first)
              </ul>
            </div>
          @endif

          @php $last_parent = true; @endphp
          <div class="m-r-20 m-b-20">
            <div class="m-t-20"><span class="parent-item">{!!$item->name!!}</span></div>
            <ul class="footer-menu">
        @else

          @if($last_parent && !$item->parent)
              </ul>
            </div>
            <div>
              <ul class="footer-menu">
          @endif

          {{-- Page Link --}}
          @if($item->type == 'page_link')
            {{-- Check if page exist --}}
            @if($item->page)
              <li><a href="{{ url('page/' . $item->page->slug ) }}">{!!$item->name!!}</a></li>
            @endif
          @else
            {{-- Internal Link --}}
            @if($item->type == 'internal_link')
              <li><a href="{{ url( $item->link ) }}">{!!$item->name!!}</a></li>
            {{-- External Link --}}
            @else
              <li><a href="{{ $item->link }}" target="_blank">{!!$item->name!!}</a></li>
            @endif
          @endif

        @endif

        {{-- Last iteration - close menu --}}
        @if($loop->last)
            </ul>
          </div>
        @endif
      @endforeach
    </div>


    <div class="no-flex-shrink">
      {{-- Language selector --}}
      @if(config('settings.locale_selector'))
        <select class="form-control select m-t-20" onChange="window.location.href=this.value" style="display: inline;
      width: inherit;">
          <option value="disabled" disabled selected>{{ trans('general.language') }}</option>
            @foreach($languages as $language)
            <option value="{{url('lang/' . $language->abbr)}}">{{$language->name}}</option>
            @endforeach
        </select>
      @endif
      {{-- Theme selector --}}
      @if(config('settings.theme_selector'))
        <select class="form-control select m-t-20 m-l-10" onChange="window.location.href=this.value" style="display: inline;
      width: inherit;">
          <option value="disabled" disabled selected>{{ trans('general.theme') }}</option>
            {{-- Get all themes --}}
            @php $themes = Theme::all(); @endphp
            @foreach($themes as $theme)
              @if($theme['public'])
                <option value="{{url('theme/' . $theme['slug'])}}">{{ $theme['name'] or 'Unknown Name' }}</option>
              @endif
            @endforeach
        </select>
      @endif
    </div>
  </div>
  <div class="social flex-center-space-wrap">
    {{-- Copyright with current year --}}
    <div class="copyright">
      © {{Carbon::now()->year}} <span class="f-w-700">{{ config('settings.page_name') }}</span>
    </div>
    <div itemscope itemtype="http://schema.org/Organization">
      {{-- Site link for google --}}
      <link itemprop="url" href="{{ url('/') }}">
      {{-- Facebook link --}}
      @if(config('settings.facebook_link'))
      <a itemprop="sameAs" class="btn btn-icon btn-round btn-dark" href="{{config('settings.facebook_link')}}" target="_blank"><i class="icon fab fa-facebook-f"></i></a>
      @endif
      {{-- Twitter link  --}}
      @if(config('settings.twitter_link'))
      <a itemprop="sameAs" class="btn btn-icon btn-round btn-dark m-l-5" href="{{config('settings.twitter_link')}}" target="_blank"><i class="fab fa-twitter"></i></a>
      @endif
      {{-- Google Plus link --}}
      @if(config('settings.google_plus_link'))
      <a itemprop="sameAs" class="btn btn-icon btn-round btn-dark m-l-5" href="{{config('settings.google_plus_link')}}" target="_blank"><i class="fab fa-google-plus-g"></i></a>
      @endif
      {{-- YouTube link --}}
      @if(config('settings.youtube_link'))
      <a itemprop="sameAs" class="btn btn-icon btn-round btn-dark m-l-5" href="{{config('settings.youtube_link')}}" target="_blank"><i class="fab fa-youtube"></i></a>
      @endif
      {{-- Instagram link --}}
      @if(config('settings.instagram_link'))
      <a itemprop="sameAs" class="btn btn-icon btn-round btn-dark m-l-5" href="{{config('settings.instagram_link')}}" target="_blank"><i class="fab fa-instagram"></i></a>
      @endif
    </div>
  </div>


</div>
