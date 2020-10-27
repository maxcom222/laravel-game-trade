<tr>
	<td colspan="3" class="bp-bg-fill-metal">
		<h5 class="bp-margin-b-0">{!! /*$level.*/ucfirst(str_replace(['_', '-'], ' ', trim($header))) !!}</h5>
	</td>
</tr>
{!! $langfile->displayInputs($item, $parents, $header, $level) !!}
