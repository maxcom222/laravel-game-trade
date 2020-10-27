<canvas style="display: none;" id="{{ $chart->id }}" {!! $chart->formatContainerOptions('html') !!} height="400"></canvas>
@include('charts::loader')
