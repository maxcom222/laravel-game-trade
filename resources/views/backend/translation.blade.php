<div class="bp-padding-b-20 bp-padding-t-20 border-top">
    <h4 class="box-title"><i class="far fa-language"></i>
        @foreach ($languages as $lang)
            @if ($currentLang == $lang->abbr)
                {{{ $lang->name }}}
            @endif
        @endforeach
        <small>
             &nbsp; switch to &nbsp;
            <select name="language_switch" id="language_switch" class="form-control form-control-sm bp-block-inline" style="width: inherit;">
                @foreach ($languages as $lang)
                <option value="{{ url(config('backpack.base.route_prefix', 'admin')."/translation/texts/{$lang->abbr}") }}" {{ $currentLang == $lang->abbr ? 'selected' : ''}}>{{ $lang->name }}</option>
                @endforeach
            </select>
        </small>
    </h4>
</div>


<div class="row">
    <div class="col-lg-3 col-xl-2">
    	<div class="bp-portlet">
            <div class="bp-portlet__body bp-portlet__body--fit">
                <ul class="bp-nav bp-nav--bold bp-nav--md-space bp-nav--v3 bp-margin-t-20 bp-margin-b-20 " >
                    @foreach ($langFiles as $file)
                        <li class="bp-nav__item">
                            <a class="bp-nav__link {{ $file['active'] ? 'active' : '' }}" href="{{ $file['url'] }}">
                                <span class="bp-nav__link-icon"><i class="fas fa-dot-circle"></i></span>
                                <span class="bp-nav__link-text">{{ $file['name'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-9 col-xl-10">
        @if (!empty($fileArray))
            <form
                method="post"
                id="lang-form"
                class="form-horizontal"
                data-required="{{ trans('admin.language.fields_required') }}"
                action="{{ url(config('backpack.base.route_prefix', 'admin')."/translation/texts/{$currentLang}/{$currentFile}") }}"
                pjax-container
                >
                {!! csrf_field() !!}
                <div class="bp-portlet">
                    <div class="table-responsive bp-margin-t-20">
                        <table class="table table-hover table-head-noborder table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ trans('backport.key') }}</th>
                                    <th>{{ trans('backport.language_text', ['language_name' => $browsingLangObj->name]) }}</th>
                                    <th>{{ trans('backport.language_translation', ['language_name' => $currentLangObj->name]) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! $langfile->displayInputs($fileArray) !!}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{ trans('backport.key') }}</th>
                                    <th>{{ trans('backport.language_text', ['language_name' => $browsingLangObj->name]) }}</th>
                                    <th>{{ trans('backport.language_translation', ['language_name' => $currentLangObj->name]) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="bp-portlet__foot">
                        <div class="bp-form__actions">

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">{{ trans('admin.reset') }}</button>
                            </div>

                            <div class="btn-group pull-right">
                                <button type="submit" class="btn btn-brand"><i class="la la-check"></i> {{ trans('admin.save') }}</button>
                            </div>


                        </div>
                    </div>
        		</div>
            </form>
        @else
        @endif

    </div>
</div>


<script>
	jQuery(document).ready(function($) {
		$("#language_switch").change(function() {
            $.pjax({url:  $(this).val(), container: '#pjax-container'});
		})
	});
</script>
