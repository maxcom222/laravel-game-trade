<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} col-form-label control-label">{{$label}}</label>

    <div class="row no-gutters {{$viewClass['field']}}">
        <div class="col-3 pr-2">
            <select id="page_or_link_select" name="type" class="form-control">
                <option value="page_link" {{ (isset($data) && $data['type'] == 'page_link') ? 'selected' : ''}}>
                    Page Link
                </option>
                <option value="internal_link" {{ (isset($data) && $data['type'] == 'internal_link') ? 'selected' : ''}}>
                    Internal Link
                </option>
                <option value="external_link" {{ (isset($data) && $data['type'] == 'external_link') ? 'selected' : ''}}>
                    External Link
                </option>
            </select>
        </div>
        <div class="col-9">
            <!-- external link input -->
              <div class="page_or_link_value {{ (!isset($data) || $data['type'] != 'external_link') ? 'd-none' : ''}}" id="page_or_link_external_link">
                <input
                    type="url"
                    class="form-control"
                    name="link"
                    placeholder="http://example.com/your-desired-page"

                    @if(!isset($data) || $data['type'] != 'external_link')
                        disabled="disabled"
                     @endif

                    @if(!isset($data) || $data['type'] == 'external_link')
                        value="{{ old($column, $value) }}"
                    @endif
                    >
              </div>
              <!-- internal link input -->
              <div class="page_or_link_value {{ (!isset($data) || $data['type'] != 'internal_link') ? 'd-none' : ''}}" id="page_or_link_internal_link">
                <input
                    type="text"
                    class="form-control"
                    name="link"
                    placeholder="Internal slug. Ex: 'page/contact' (no quotes)"

                    @if(!isset($data) || $data['type'] != 'internal_link')
                        disabled="disabled"
                    @endif

                    @if(!isset($data) || $data['type'] == 'internal_link')
                        value="{{ old($column, $value) }}"
                    @endif
                    >
              </div>
              <!-- page slug input -->
              <div class="page_or_link_value {{ (isset($data) && ($data['type'] != 'page_link' && $data['type'] != '')) ? 'd-none' : ''}}" id="page_or_link_page">
                <select
                    class="form-control"
                    name="page_id"
                    >
                    @if (!count($pages))
                        <option value="0">No pages available</option>
                    @else
                        @foreach ($pages as $key => $page)
                            <option value="{{ $page->id }}"
                                {{ (isset($data) && $data['page_id'] == $page->id) ? 'selected' : ''}}
                            >{{ $page->name }}</option>
                        @endforeach
                    @endif

                </select>
              </div>

        </div>
    </div>

    @include('backport::form.error')
    @include('backport::form.help-block')

</div>
