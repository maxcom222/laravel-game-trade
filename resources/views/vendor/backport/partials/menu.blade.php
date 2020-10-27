{{--Start Custom GamePort Script--}}
@if($item['uri'] == 'reports')
    @php $open_reports = \App\Models\Report::where('status','0')->count(); @endphp
@elseif($item['uri'] == 'withdrawals' || ($item['uri'] == 'payments' && $item['parent_id'] == '0'))
    @php isset($pending_withdrawals) ? '' : $pending_withdrawals = \App\Models\Withdrawal::where('status','1')->count(); @endphp
@endif
{{--End Custom GamePort Script--}}

@if(Backport::user()->visible($item['roles']) && (empty($item['permission']) ?: Backport::user()->can($item['permission'])))
    {{--Start Custom GamePort Script--}}
    @if((config('settings.location_api') == 'zippopotam' && $item['uri'] == 'settings/countries') || $item['uri'] != 'settings/countries')
    {{--End Custom GamePort Script--}}
        @if(!isset($item['children']))
            {{-- Menu Seperator (Item without URI) --}}
            @if(!isset($item['uri']) || (isset($item['uri']) && $item['uri'] == ""))
              <li class="bp-menu__section ">
                <h4 class="bp-menu__section-text">{{ $item['title'] }}</h4>
                <i class="bp-menu__section-icon fas fa-ellipsis-h"></i>
              </li>
            @else
                <li class="bp-menu__item {{ request()->is(substr(admin_base_path($item['uri']), 1) . '*') && $item['uri'] != '/' ? 'bp-menu__item--active' : '' }}" aria-haspopup="true">
                    @if(url()->isValidUrl($item['uri']))
                        <a href="{{ $item['uri'] }}" target="_blank"  class="bp-menu__link">
                    @else
                         <a href="{{ admin_base_path($item['uri']) }}"  class="bp-menu__link">
                    @endif
                        @if($item['parent_id'])
                            <i class="bp-menu__link-bullet bp-menu__link-bullet--dot"><span></span></i>

                        @else
                            <i class="bp-menu__link-icon fa {{$item['icon']}}"></i>
                        @endif
                        @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                            <span class="bp-menu__link-text">{{ __($titleTranslation) }}</span>
                        @else
                            <span class="bp-menu__link-text">{{ $item['title'] }}</span>
                        @endif
                        {{--Start Custom GamePort Script--}}
                        @if($item['uri'] == 'reports' && $open_reports > 0)
                            <span class="bp-menu__link-badge"><span class="bp-badge bp-badge--danger">{{ $open_reports }}</span></span>
                        @elseif($item['uri'] == 'withdrawals' && $pending_withdrawals > 0)
                            <span class="bp-menu__link-badge"><span class="bp-badge bp-badge--warning">{{ $pending_withdrawals }}</span></span>
                        @endif
                        {{--End Custom GamePort Script--}}
                    </a>
                </li>
            @endif
        @else
            <li class="bp-menu__item  bp-menu__item--submenu" aria-haspopup="true">
                <a href="javascript:;" class="bp-menu__link bp-menu__toggle">
                    <i class="bp-menu__link-icon fa {{ $item['icon'] }}"></i>
                    @if (Lang::has($titleTranslation = 'admin.menu_titles.' . trim(str_replace(' ', '_', strtolower($item['title'])))))
                        <span class="bp-menu__link-text">{{ __($titleTranslation) }}</span>
                    @else
                        <span class="bp-menu__link-text">{{ $item['title'] }}</span>
                    @endif
                    {{--Start Custom GamePort Script--}}
                    @if(($item['uri'] == 'payments' && $item['parent_id'] == '0')  && $pending_withdrawals > 0)
                        <span class="bp-menu__link-badge"><span class="bp-badge bp-badge--warning">{{ $pending_withdrawals }}</span></span>
                    @endif
                    {{--End Custom GamePort Script--}}
                    <i class="bp-menu__ver-arrow la la-angle-right"></i>
                </a>
                <div class="bp-menu__submenu "><span class="bp-menu__arrow"></span>
                    <ul class="bp-menu__subnav">
                        @foreach($item['children'] as $item)
                            @include('backport::partials.menu', $item)
                        @endforeach
                    </ul>
                <div>
            </li>
        @endif
    {{--Start Custom GamePort Script--}}
    @endif
    {{--End Custom GamePort Script--}}
@endif
