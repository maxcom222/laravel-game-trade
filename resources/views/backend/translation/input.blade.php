<tr>
	<td style="width: 1px;white-space: nowrap">
		<span class="badge badge-secondary">{{ str_replace(['_', '-'], ' ', $key) }}</span>
	</td>

	<td style="max-width: 150px;">
		@php
		if (count($parents)) {
			$parents_array = implode('.', $parents);
			$string_text = trans($lang_file_name . '.' . $parents_array . '.' . $key);
		} else {
			$string_text = trans($lang_file_name . '.' .$key);
		}
		echo htmlentities($string_text);
		@endphp
	</td>

	<td>
		@if (preg_match('/(\|)/', $item))
			@php
			$chuncks = explode('|', $item);
			@endphp

			@foreach ($chuncks as $k => $chunck)
				@php
				preg_match('/^({\w}|\[[\w,]+\])([\w\s:]+)/', trim($chunck), $m);
				@endphp
				@if (empty($m))
					<label for="{{ $chunck }}" class="col-sm-2 control-label">{{ (!$k ? trans('admin.language.singular') : trans('admin.language.plural')).":" }}</label>
					<textarea name="{{ (empty($parents) ? $key : implode('__', $parents)."__{$key}")."[after][]" }}" class="form-control" rows="2"> {{ $chunck }} </textarea>
					<br>
				@else
					<label for="{{ $chunck }}" class="col-sm-2 control-label">{{ (!$k ? trans('admin.language.singular') : trans('admin.language.plural'))." ($m[1]):" }}</label>
					<input type="hidden" name="{{ (empty($parents) ? $key : implode('__', $parents)."__{$key}")."[before][]" }}" value="{{ $m[1] }}">
					<textarea name="{{ (empty($parents) ? $key : implode('__', $parents)."__{$key}")."[after][]" }}" class="form-control" rows="2"> {{ $m[2] }} </textarea>
					<br>
				@endif
			@endforeach
		@else
			<textarea name="{{ (empty($parents) ? $key : implode('__', $parents)."__{$key}") }}" class="form-control" rows="2"> {{ $item }} </textarea>
			<br>
		@endif
	</td>

</tr>
