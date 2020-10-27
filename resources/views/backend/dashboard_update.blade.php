@if($version_response <= config('settings.script_version'))
    <div>
        <h5 class="text-success"><i class="icon fa fa-check"></i> GamePort Script Up to Date</h5>
        You are running the latest GamePort script version <strong>{{config('settings.script_version')}}</strong>!
    </div>
    <div class="text-right">
         <strong> {{\Carbon\Carbon::now()->format(config('settings.date_format'))}} </strong><br />Last check
    </div>
@else
    <div>
        <h5 class="text-danger"><i class="icon fa fa-ban"></i> GamePort Script update available!</h5>
        Your are running an old GamePort script version ({{config('settings.script_version')}}). Please update to version <strong>{{ $version_response }}</strong>!
    </div>
    <div class="text-right">
         <strong> {{\Carbon\Carbon::now()->format(config('settings.date_format'))}} </strong><br />Last check
    </div>
@endif
