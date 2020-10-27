

@foreach ($chart->plugins as $plugin)
    @include($chart->pluginsViews[$plugin]);
@endforeach

<script {!! $chart->displayScriptAttributes() !!}>
$(function () {
    var ctvChart = document.getElementById('{{ $chart->id }}').getContext('2d');

    var {{ $chart->id }} = new Chart(ctvChart, {
        type: {!! $chart->type ? "'{$chart->type}'" : "'{$chart->formatType()}'" !!},
        data: {
            labels: {!! $chart->formatLabels() !!},
            datasets: {!! $chart->formatDatasets() !!}
        },
        options: {!! $chart->formatOptions(true) !!},
        plugins: {!! $chart->formatPlugins(true) !!}
    });

    document.getElementById("{{ $chart->id }}_loader").style.display = 'none';
    document.getElementById("{{ $chart->id }}").style.display = 'block';

});
</script>
