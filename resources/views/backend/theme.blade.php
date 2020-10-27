<div class="row">
	@foreach($themes as $theme)
	  <div class="col-md-6 col-lg-6 col-xl-4">
			<div class="bp-portlet">
				<div class="bp-portlet__head">
					<div class="bp-portlet__head-label">
						<span class="bp-portlet__head-icon">
							<i class="fas fa-pencil-ruler"></i>
						</span>
						<h3 class="bp-portlet__head-title">
							{{ $theme['name'] ?? 'Unknown Name' }}
							<small>{{ $theme['slug'] ?? 'Unknown Slug' }}</small>
						</h3>
					</div>

					<div class="bp-portlet__head-toolbar">
						{{ $theme['version'] ?? 'Unknown Version' }}
					</div>
				</div>
				<div class="bp-portlet__body theme">
					<div class="bp-portlet__content">
						<object data="{{ asset('themes/' . $theme['slug'] . '/screenshot.jpg') }}" type="image/jpg" class="theme-screenshot screenshot-object">
	                        <img src="{{ asset('themes/default_screenshot.jpg') }}" class="m-b-10 theme-screenshot" style="" />
	                    </object>
						@if($theme['gameport_version'] && (config('settings.script_version') >=  $theme['gameport_version']))
	                      <div class="alert alert-success">
							<div class="alert-icon">
								<i class="fa fa-check" aria-hidden="true"></i>
							</div>
							<div class="alert-text">
								Compatible with this GamePort script version.
							</div>
	                      </div>
	                    @elseif($theme['gameport_version'] && (config('settings.script_version') <  $theme['gameport_version']))
	                      <div class="alert alert-danger">
						   <div class="alert-icon">
							   <i class="fa fa-times" aria-hidden="true"></i>
						   </div>
						   <div class="alert-text">
							   Not compatible with this GamePort script version. You need at least version <strong>{{ $theme['gameport_version'] }}</strong>.
						   </div>
	                      </div>
	                    @elseif(!$theme['gameport_version'])
	                      <div class="alert alert-warning">
							 <div class="alert-icon">
								 <i class="fa fa-info-circle" aria-hidden="true"></i>
							 </div>
							 <div class="alert-text">
								 Unknown GamePort script version.
							 </div>
	                      </div>
	                    @endif
	                    <strong>Author: </strong>{{ $theme['author'] ?? 'Unknown Author' }}<br>
	                    <strong>Description: </strong>{{ $theme['description'] ?? 'No Description' }}
					</div>
				</div>
				<div class="bp-portlet__foot">
					<div class="row align-items-center">
						<div class="col-lg-6">
							@if($theme['public'])
	                          <i class="fa fa-check"></i> Public
	                        @else
	                          <i class="fa fa-times"></i> Not Public
	                        @endif
						</div>
						<div class="col-lg-6 bp-align-right">
							@if($theme['gameport_version'] && (config('settings.script_version') >=  $theme['gameport_version']))
	                          @if(config('settings.default_theme') == $theme['slug'])
	                  			<div class="btn btn-success">
	                              <i class="fa fa-check"></i> Default Theme
							  	</div>
	                          @else
	                            @if($theme['public'])
	                              <a class="btn btn-default" href="{{ url('admin/settings/theme' , $theme['slug']) }}">
	                                <i class="fa fa-check"></i> Set as Default
	                              </a>
	                            @else
									<div class="btn btn-danger">
		                             	<i class="fa fa-times"></i> Not Public
		                         	</div>
	                            @endif
	                          @endif
	                        @else
	                          <div class="btn btn-danger">
	                            <i class="fa fa-times"></i> Not Compatible
	                          </div>
	                        @endif
						</div>
					</div>
				</div>
			</div>
		</div>
	@endforeach
</div>
