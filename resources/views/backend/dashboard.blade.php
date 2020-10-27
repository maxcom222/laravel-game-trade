@if($security)
  <div class="alert alert-danger fade show" role="alert">
	  <div class="alert-icon"><i class="fas fa-do-not-enter"></i></div>
	  <div class="alert-text">
		  <h4 class="alert-heading"> Security Alert!</h4>
		  <p>Please set the permissions of the /.env and/or /config/app.php files to 0664.</p>
	  </div>
	  <div class="alert-close">
		  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true"><i class="fa fa-times"></i></span>
		  </button>
	  </div>
  </div>
@endif

@if($giantbomb)
	<div class="alert alert-warning fade show" role="alert">
	  	<div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
		<div class="alert-text">
			<h4 class="alert-heading"> GiantBomb Warning!</h4>
			<p>Please set your GiantBomb API Key in the <a href="{{ url(config('backpack.base.route_prefix', 'admin').'/settings/game') }}">settings</a>. Otherwise, the game adding feature may not work properly.</p>
		</div>
	    <div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true"><i class="fa fa-times"></i></span>
			</button>
		</div>
	</div>
@endif

<div class="row">
	{{-- Start Listings Stat Widget --}}
	<div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">
		{{-- Portlet --}}
        <div class="bp-portlet bp-portlet--fit bp-portlet--height-fluid bp-bg-success">
        	<div class="bp-portlet__body bp-portlet__body--fluid">
        		<div class="bp-stats-widget bp-stats-widget--success">
					{{-- Icon --}}
					<div class="bp-stats-widget__icon">
						<i class="fa fa-tags"></i>
					</div>
        			<div class="bp-stats-widget__content">
        				<div class="bp-stats-widget__content-info" >
        					<div class="bp-stats-widget__content-section">
								{{-- Number --}}
        						<div class="bp-stats-widget__content-number">{{$listings}}</div>
								{{-- Title --}}
        						<div class="bp-stats-widget__content-title">Listings</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
			{{-- Graph --}}
            <div class="bp-stats-widget__graph">
				{!! $listings_top->container() !!}
            </div>
        </div>
    </div>
	{{-- End Listings Stat Widget --}}

	{{-- Start Offers Stat Widget --}}
	<div class="col-lg-3 col-xl-3 order-lg-2 order-xl-2">
		{{-- Portlet --}}
        <div class="bp-portlet bp-portlet--fit bp-portlet--height-fluid bp-bg-brand">
        	<div class="bp-portlet__body bp-portlet__body--fluid">
        		<div class="bp-stats-widget bp-stats-widget--brand">
					{{-- Icon --}}
					<div class="bp-stats-widget__icon">
						<i class="fa fa-briefcase"></i>
					</div>
        			<div class="bp-stats-widget__content">
        				<div class="bp-stats-widget__content-info" >
        					<div class="bp-stats-widget__content-section">
								{{-- Number --}}
        						<div class="bp-stats-widget__content-number">{{$offers}}</div>
								{{-- Title --}}
        						<div class="bp-stats-widget__content-title">Offers</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
			{{-- Graph --}}
            <div class="bp-stats-widget__graph">
				{!! $offers_top->container() !!}
            </div>
        </div>
    </div>
	{{-- End Offers Stat Widget --}}

	{{-- Start Games Stat Widget --}}
	<div class="col-lg-3 col-xl-3 order-lg-3 order-xl-3">
		{{-- Portlet --}}
        <div class="bp-portlet bp-portlet--fit bp-portlet--height-fluid bp-bg-danger">
        	<div class="bp-portlet__body bp-portlet__body--fluid">
        		<div class="bp-stats-widget bp-stats-widget--danger">
					{{-- Icon --}}
					<div class="bp-stats-widget__icon">
						<i class="fa fa-gamepad"></i>
					</div>
        			<div class="bp-stats-widget__content">
        				<div class="bp-stats-widget__content-info" >
        					<div class="bp-stats-widget__content-section">
								{{-- Number --}}
        						<div class="bp-stats-widget__content-number">{{$games}}</div>
								{{-- Title --}}
        						<div class="bp-stats-widget__content-title">Games</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
			{{-- Graph --}}
            <div class="bp-stats-widget__graph">
				{!! $games_top->container() !!}
            </div>
        </div>
    </div>
	{{-- End Games Stat Widget --}}

	{{-- Start Users Stat Widget --}}
	<div class="col-lg-3 col-xl-3 order-lg-4 order-xl-4">
		{{-- Portlet --}}
        <div class="bp-portlet bp-portlet--fit bp-portlet--height-fluid bp-bg-warning">
        	<div class="bp-portlet__body bp-portlet__body--fluid">
        		<div class="bp-stats-widget bp-stats-widget--warning">
					{{-- Icon --}}
					<div class="bp-stats-widget__icon">
						<i class="fa fa-users"></i>
					</div>
        			<div class="bp-stats-widget__content">
        				<div class="bp-stats-widget__content-info" >
        					<div class="bp-stats-widget__content-section">
								{{-- Number --}}
        						<div class="bp-stats-widget__content-number text-white">{{$users}}</div>
								{{-- Title --}}
        						<div class="bp-stats-widget__content-title text-white">Users</div>
        					</div>
        				</div>
        			</div>
        		</div>
        	</div>
			{{-- Graph --}}
            <div class="bp-stats-widget__graph">
				{!! $users_top->container() !!}
            </div>
        </div>
    </div>
	{{-- End Users Stat Widget --}}

	{{-- Start statistics --}}
	<div class="col-lg-12 col-xl-12 order-lg-5 order-xl-5">
		<div class="bp-portlet bp-portlet--height-fluid">
			<div class="bp-portlet__body">
				<div class="bp-stats-lg">
					<div class="row">

						{{-- Complete payments --}}
						<div class="col-xl-3 col-lg-6 col-md-6 col-6">
							<div class="bp-stats-lg__item bp-stats-lg__item--warning">
								<div class="bp-stats-lg__labels">
									<div class="bp-stats-lg__title">Complete Payments</div>
									<div class="bp-stats-lg__desc">last 7 days</div>
								</div>
								<div class="bp-stats-lg__data">
									<div class="bp-stats-lg__numbers">
										<div class="bp-stats-lg__numbers-total">{{ $payments }}</div>
										<div class="bp-stats-lg__numbers-change">{{ $payments_last }}</div>
									</div>
								</div>
							</div>
						</div>

						{{-- Received money --}}
						<div class="col-xl-3 col-lg-6 col-md-6 col-6">
							<div class="bp-stats-lg__item bp-stats-lg__item--info">
								<div class="bp-stats-lg__labels">
									<div class="bp-stats-lg__title">Received Money</div>
									<div class="bp-stats-lg__desc">last 7 days</div>
								</div>
								<div class="bp-stats-lg__data">
									<div class="bp-stats-lg__numbers">
										<div class="bp-stats-lg__numbers-total">{{ money(abs(filter_var(number_format($payments_sum, 2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
										<div class="bp-stats-lg__numbers-change">{{ money(abs(filter_var(number_format($payments_last_sum, 2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
									</div>
								</div>
							</div>
						</div>

						{{-- Transaction fees --}}
						<div class="col-xl-3 col-lg-6 col-md-6 col-6">
							<div class="bp-stats-lg__item bp-stats-lg__item--danger">
								<div class="bp-stats-lg__labels">
									<div class="bp-stats-lg__title">Transaction Fees</div>
									<div class="bp-stats-lg__desc">last 7 days</div>
								</div>
								<div class="bp-stats-lg__data">
									<div class="bp-stats-lg__numbers">
										<div class="bp-stats-lg__numbers-total">{{ money(abs(filter_var(number_format($transaction_fees = $payments_sum_fee, 2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
										<div class="bp-stats-lg__numbers-change">{{ money(abs(filter_var(number_format($transaction_fees_last = $payments_last_sum_fee, 2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
									</div>
								</div>
							</div>
						</div>

						{{-- Earning --}}
						<div class="col-xl-3 col-lg-6 col-md-6 col-6">
							<div class="bp-stats-lg__item bp-stats-lg__item--success">
								<div class="bp-stats-lg__labels">
									<div class="bp-stats-lg__title">Earnings</div>
									<div class="bp-stats-lg__desc">last 7 days</div>
								</div>
								<div class="bp-stats-lg__data">
									<div class="bp-stats-lg__numbers">
										<div class="bp-stats-lg__numbers-total">{{ money(abs(filter_var(number_format($transactions - $transaction_fees,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
										<div class="bp-stats-lg__numbers-change">{{ money(abs(filter_var(number_format($transactions_last - $transaction_fees_last,2), FILTER_SANITIZE_NUMBER_INT)), config('settings.currency'))->format(true) }}</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="bp-stats-lg__stats-label">
						<div class="bp-stats-lg__stats-label-author">
							<div class="bp-stats-lg__stats-label-bullet bp-bg-success"></div>
							<span class="bp-stats-lg__stats-label-text">Listings</span>
						</div>
						<div class="bp-stats-lg__stats-label-product">
							<div class="bp-stats-lg__stats-label-bullet bp-bg-brand"></div>
							<span class="bp-stats-lg__stats-label-text">Offers</span>
						</div>
					</div>
				</div>
			</div>
			{{-- Graph --}}
			<div class="bp-portlet__body bp-portlet__body--stic bp-bottom bp-portlet__body--fit" style="position: relative;"><div class="chartjs-size-monitor" style="position: absolute; left: 0px; top: 0px; right: 0px; bottom: 0px; overflow: hidden; pointer-events: none; visibility: hidden; z-index: -1;"><div class="chartjs-size-monitor-expand" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:1000000px;height:1000000px;left:0;top:0"></div></div><div class="chartjs-size-monitor-shrink" style="position:absolute;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1;"><div style="position:absolute;width:200%;height:200%;left:0; top:0"></div></div></div>
				{!! $general_stats->container() !!}
			</div>
		</div>
	</div>
	{{-- End statistics --}}

	{{-- Start latest listings --}}
	<div class="col-lg-12 col-xl-7 order-lg-6 order-xl-6">
		<div class="bp-portlet bp-portlet--height-fluid bp-widget-17">
			<div class="bp-portlet__head">
				<div class="bp-portlet__head-label">
					<h3 class="bp-portlet__head-title">Latest Listings</h3>
				</div>
			</div>
			<div class="bp-portlet__body bp-portlet__body--fit bp-padding-t-10 bp-padding-b-10">
				{{-- take last 5 listings --}}
				@foreach($listings_last as $listing)
					<div class="d-flex justify-content-between align-items-center bp-padding-t-10  bp-padding-b-10  bp-padding-r-20  bp-padding-l-20">
						<div class="image-text">
						    <img src="{{ $listing->game->image_square_tiny }}" />
						    <div class="content">
						        <div class="top">
						            <strong><a href="{{ $listing->url_slug }}" target="_blank">{{ $listing->game->name }}</a></strong>
						        </div>
						        <div class="bottom">
						            <span class="badge badge-dark" style="background-color: {{ $listing->game->platform->color }}; margin-right: 10px;">{{ $listing->game->platform->name }}</span><i class="fa fa-calendar"></i> {{ $listing->game->release_date->format('Y') }}
						        </div>
						    </div>
						</div>
						<div>
							@if($listing->sell)
								<span class="badge badge-success bp-font-lg">{{ $listing->price_formatted }}</span>
							@endif
							@if($listing->trade)
								<span class="badge badge-warning bp-font-lg ml-1"><i class="fa fa-exchange"></i></span>
							@endif
						</div>

					</div>
				@endforeach
			</div>
		</div>
	</div>
	{{-- End latest listings --}}

	{{-- Start recent registrations --}}
	<div class="col-lg-12 col-xl-5 order-lg-7 order-xl-7">
		<div class="bp-portlet bp-portlet--height-fluid">
		    <div class="bp-portlet__head">
		        <div class="bp-portlet__head-label">
		            <h3 class="bp-portlet__head-title">Recent Registrations</h3>
		        </div>
		    </div>
		    <div class="bp-portlet__body bp-portlet__body--fit bp-padding-t-10 bp-padding-b-10">
				<div class="row">
					<div class="col-xs-6 col-lg-6">
						@foreach($users_last->take(5) as $user)
							<div class="image-text bp-padding-t-10  bp-padding-b-10  bp-padding-l-20">
							    <img src="{{ $user->avatar_square_tiny }}" />
							    <div class="content">
							        <div class="top">
							            <strong><a href="{{ $user->url }}" target="_blank" class="">{{ $user->name }}</a></strong>
							        </div>
							        <div class="bottom">
							           	@if($user->isOnline())
											<i class="fa fa-circle text-success"></i> Online
										@else
											<i class="fa fa-circle text-danger"></i> Offline
										@endif
							        </div>
							    </div>
							</div>
						@endforeach
					</div>
					<div class="col-xs-6 col-lg-6">
						@foreach($users_last->slice(5)->take(5) as $user)
							<div class="image-text bp-padding-t-10  bp-padding-b-10  bp-padding-l-20  bp-padding-r-20">
							    <img src="{{ $user->avatar_square_tiny }}" />
							    <div class="content">
							        <div class="top">
							            <strong><a href="{{ $user->url }}" target="_blank" class="">{{ $user->name }}</a></strong>
							        </div>
							        <div class="bottom">
							           	@if($user->isOnline())
											<i class="fa fa-circle text-success"></i> Online
										@else
											<i class="fa fa-circle text-danger"></i> Offline
										@endif
							        </div>
							    </div>
							</div>
						@endforeach
					</div>
				</div>
		    </div>
		</div>
	</div>
	{{-- End recent registrations --}}

	{{-- Start checking for updates --}}
	<div class="col-lg-12 col-xl-12 order-lg-8 order-xl-8">
		<div class="bp-portlet">
			<div class="bp-portlet__body bp-portlet__body--fit d-flex flex-row align-items-center">
				<div class="bp-padding-20 bp-font-xl border-right">
					<i class="fas fa-sync-alt fa-spin" id="update-loading"></i>
				</div>
				<div class="bp-padding-l-20 bp-padding-r-20 d-flex justify-content-between align-items-center" style="width:100%;" id="update-check">
					Checking for updates...
				</div>
			</div>
		</div>
	</div>
	{{-- End checking for updates --}}

</div>

{{-- Script for checking updates --}}
<script>
$('#update-check').load('{{ route('check.update') }}', function() {
  $('#update-loading').removeClass('fa-spin');
});
</script>

{{-- Stats scripts --}}
{!! $listings_top->script() !!}
{!! $offers_top->script() !!}
{!! $games_top->script() !!}
{!! $users_top->script() !!}
{!! $general_stats->script() !!}
