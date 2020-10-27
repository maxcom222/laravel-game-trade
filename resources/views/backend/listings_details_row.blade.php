<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
	<div class="row">
		<div class="col-md-12">
			@if($trade_list)
				@foreach($trade_list as $game)
					<div class="user-block m-r-20 m-b-20 p-10" style="display: inline-block; background-color: rgba(0,0,0,0.05); padding: 10px; border-radius: 5px; border: 1px solid rgba(0,0,0,0.1); ">
						<img class="img-circle" src="{{$game->image_square_tiny}}" alt="User Image">
						<span class="username">{{$game->name}}</span>
						<span class="description"><span class="label" style="background-color: {{$game->platform->color}}; margin-right: 10px;">{{$game->platform->name}}</span><i class="fa fa-calendar"></i> {{$game->release_date->format('Y')}}</span>
					</div>
				@endforeach
			@else
				<span><i class="fa fa-times"></i> No trade list available</span>
			@endif
		</div>
	</div>
</div>
<div class="clearfix"></div>
