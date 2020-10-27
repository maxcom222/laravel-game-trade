{{-- START LOCATION MODAL --}}

{{ Request::is('dash/settings') ? $force = false : $force = true }}

{{-- START modal for user location --}}
<div class="modal @if($force) modal-danger @else modal-success @endif fade modal-super-scaled" id="modal_user_location" @if($force) data-backdrop="static" data-keyboard="false" @endif>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {{-- Open form to save location --}}
      {!! Form::open(array('url'=>'dash/settings/location', 'id'=>'form-savelocation', 'role'=>'form', 'parsley-validate'=>'','novalidate'=>' ')) !!}

      {{-- Start Modal header --}}
      <div class="modal-header">

        <div class="background-pattern" style="background-image: url('{{ asset('/img/game_pattern.png') }}');"></div>

        <div class="title">
          {{-- Close button redirect to previous page or homepage --}}
          @if($force)
          <a href="@if(URL::previous() == URL::current()) {{ url('/') }} @else {{ URL::previous() }} @endif" class="close" >
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
          </a>
          @else
          <a data-dismiss="modal" data-toggle="modal" class="close" href="javascript:void(0)">
            <span aria-hidden="true">×</span><span class="sr-only">{{ trans('general.close') }}</span>
          </a>
          @endif
          {{-- Modal title (Set Location) --}}
          <h4 class="modal-title" id="myModalLabel">
            <i class="fa fa-map-marker" aria-hidden="true"></i>
            {{ trans('users.modal_location.title') }}
          </h4>
        </div>

      </div>
      {{-- End Modal header --}}

      {{-- Start Modal body --}}
      <div class="modal-body" style="z-index: 2 !important;">

        {{-- Start Location form --}}
        <div class="form-group" id="selectlocation" style="margin-bottom: 0 !important;">
          @if($force)
          {{-- Info --}}
          <div class="m-b-20">{{ trans('users.modal_location.info') }}</div>
          @endif
          {{-- Country selection --}}
          {{-- Get all countries from database --}}
          @php $countries =  \App\Models\Country::orderBy('lft')->get(); @endphp
          <select class="form-control select m-b-20" id="country" name="country" {{$countries->count() == 1 ? 'disabled' : '' }}>
            <option value="disabled" disabled {{$countries->count() > 1 || $countries->count() == 0  ? 'selected' : ''}}>{{ trans('users.modal_location.placeholder.country') }}</option>
            @foreach($countries as $country)
              <option value="{{$country->code}}" {{$countries->count() == 1 ? 'selected' : ''}}>{{$country->name}}</option>
            @endforeach
          </select>

          {{-- Start postal code form and city selection --}}
          <div class="row {{ $countries->count() > 1 || $countries->count() == 0  ? 'hidden' : '' }}" id="postalcode_form">
            {{-- Postal code input --}}
            <div class="form-group col-xs-4 postal-code-input">
              <input name="postalcode" type="text" id="postalcode" placeholder="{{ trans('users.modal_location.placeholder.postal_code') }}" class="form-control input" autocomplete="off" />
            </div>
            {{-- Location selection --}}
            <div class="form-group col-xs-8 locality-select">
              <select class="form-control select" id="locality" name="locality" disabled>
               <option selected="selected">&larr; {{ trans('users.modal_location.placeholder.postal_code_locality') }}</option>
              </select>
            </div>
            {{-- Search status --}}
            <div class="col-xs-12">

              <div class="locality-search-status bg-dark m-b-20 hidden" id="status">
              </div>

            </div>

          </div>
          {{-- End postal code form and city selection --}}

          {{-- Selected location --}}
          <div class="selected-location hidden" id="selectedlocation_panel">
            <span>{{ trans('users.modal_location.selected_location') }}</span>
            <div>
              <i class="fa fa-map-marker" aria-hidden="true"></i> <span id="selectedlocation"> </span>
            </div>
          </div>
        </div>
        {{-- End Location form --}}

        {{-- Location saved message --}}
        <div class="location-saved hidden" id="savedlocation">
            <div class="icon text-success">
              <i class="fa fa-check-circle" aria-hidden="true"></i>
            </div>
            <div class="text">
              {{ trans('users.modal_location.location_saved') }}
            </div>
        </div>

      </div>
      {{-- End Modal body --}}

      {{-- Start Modal footer for form --}}
      <div class="modal-footer" id="selectlocationfooter">
        @if($force)
        <a href="@if(URL::previous() == URL::current()) {{ url('/') }} @else {{ URL::previous() }} @endif" class="btn btn-dark btn-lg btn-animate btn-animate-vertical" ><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }} </span></a>
        @else
        <a data-dismiss="modal" data-toggle="modal" href="javascript:void(0)" class="btn btn-dark btn-lg btn-animate btn-animate-vertical" ><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('general.cancel') }} </span></a>
        @endif
        <button class="btn @if($force) btn-danger @else btn-success @endif  btn-lg btn-animate btn-animate-vertical" type="submit" disabled>
            <span><i class="icon fa fa-check" aria-hidden="true"></i> {{ trans('users.modal_location.set_location') }}
            </span>
        </button>
        {!! Form::close() !!}
      </div>
      {{-- End Modal footer for form --}}

      {{-- Start Modal footer for saved location --}}
      <div class="modal-footer hidden" id="savedlocationfooter">
        <span style="opacity: 0.5">{{ trans('users.modal_location.close_sec_1') }} <span class="c" id="10"></span> {{ trans('users.modal_location.close_sec_2') }} </span>
        @if($force)
        <a data-dismiss="modal" class="btn btn-dark btn-animate btn-animate-vertical"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('users.modal_location.close_now') }}</span></a>
        @else
        <a onClick="window.location.href=window.location.href" class="btn btn-dark btn-animate btn-animate-vertical"><span><i class="icon fa fa-times" aria-hidden="true"></i> {{ trans('users.modal_location.close_now') }}</span></a>
        @endif
      </div>
      {{-- End Modal footer for saved location --}}

    </div>
  </div>
</div>
{{-- END modal for user location --}}
{{-- END LOCATION MODAL --}}


{{-- START LOCATION CHECK SCRIPT --}}

<script type="text/javascript">
$(document).ready(function(){

@if($force)
  {{-- Open modal for user location --}}
  $("#modal_user_location").modal();
@endif

  {{-- Slide down form when country is selected --}}
  $('#country').bind('change', function () {
    if ($(this).val() != 'disabled') {
      $('#postalcode_form').slideDown('fast');
    }
  });

  {{-- Location data (JSON) --}}
  var location_data = null;
  {{-- Status --}}
  var status = $('#status');
  {{-- Locality selector --}}
  var locality = $('#locality');

  $("#postalcode").keyup(function () {
    if($(this).val().length > 2 && $('#country').val() != null ) {
      $.ajax({
        url: 'https://api.zippopotam.us/' + $('#country').val() + '/' + $(this).val(),
        dataType: "json",
        type: "GET",
        beforeSend: function(){
          $('[type="submit"]').prop('disabled', true);
          status.removeClass('bg-danger').removeClass('bg-dark').addClass('bg-dark').show();
          status.html('<i class="fa fa-spinner fa-pulse"></i> {{ trans('users.modal_location.status.searching') }}');
          locality.empty().append('<option selected="selected" value="whatever">{{ trans('users.modal_location.status.searching_place') }}</option>');
        },
        success: function(data) {
          location_data = data;
          locality.removeAttr('disabled');
          status.removeClass('bg-dark').removeClass('bg-danger').addClass('bg-success');
          if(data['places'].length == 1){
            status.html('<i class="fa fa-check"></i> {{ trans('users.modal_location.status.location_found') }}');
          }else{
            status.html('<i class="fa fa-check"></i> ' + data['places'].length +' {{ trans('users.modal_location.status.locations_found') }}');
          }
          locality.empty();
          $.each(data['places'], function(i, value) {
            if(i == 0){
              location_data.place = value['place name'];
              location_data.longitude = value['longitude'];
              location_data.latitude = value['latitude'];
              locality.append($('<option>').text(value['place name']).attr('value', value['place name']).attr('data-lat', value['latitude']).attr('data-long', value['longitude']).attr('selected', 'selected'));
              $('#selectedlocation').text( ' ' + $("[name='postalcode']").val() + ' '+ value['place name'] +', '+ data.country +'');
              $('#selectedlocation_panel').slideDown('fast');
            }else{
              locality.append($('<option>').text(value['place name']).attr('value', value['place name']).attr('data-lat', value['latitude']).attr('data-long', value['longitude']));
            }
          });
          $('[type="submit"]').prop('disabled', false);
        },
        error: function (data) {
          status.removeClass('bg-dark').removeClass('bg-success').addClass('bg-danger');
          status.html('<i class="fa fa-times"></i> {{ trans('users.modal_location.status.no_location_found') }}');
          $('#selectedlocation_panel').slideUp('fast');
          $('[type="submit"]').prop('disabled', true);
          locality.attr('disabled', 'disabled');
          locality.empty().append('<option selected="selected" value="whatever">{{ trans('users.modal_location.status.no_location_found') }}</option>');
        }

      });
    }else{
      status.show().removeClass('bg-danger').removeClass('bg-success').addClass('bg-dark');
      status.html('<i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('users.modal_location.status.search_info') }}');
      $('#selectedlocation_panel').slideUp('fast');
      $('[type="submit"]').prop('disabled', true);
      locality.attr('disabled', 'disabled');
      locality.empty().append('<option selected="selected" value="whatever">&larr; {{ trans('users.modal_location.placeholder.postal_code_locality') }}</option>');
      $('#errorlocation').hide();
    }
  });

  {{-- CHANGE LOCATION ON SELECT CHANGE --}}
  locality.change(function(){
    var selected = $(this).find('option:selected');
    location_data.place = $(this).val();
    location_data.longitude = selected.data('long');
    location_data.latitude = selected.data('lat');
    var lat = selected.data('lat');
    var long = selected.data('long');
    $("[name='location']").val(lat + ',' + long);
    $('#selectedlocation').text( ' ' + $("[name='postalcode']").val() + ' '+ $(this).val() +', '+ $('#country option:selected').text() +'');
  });


  {{-- CLOSE WINDOW COUNTER --}}
  function c(){
      var n=$('.c').attr('id');
      var c=n;
      $('.c').text(c);
      setInterval(function(){
          c--;
          if(c>=0){
              $('.c').text(c);
          }
          if(c==0){
              $('.c').text(n);
          }
      },1000);
  };


  {{-- process the form --}}
  $('#form-savelocation').submit(function(event) {

    console.log(location_data);

    {{-- process the form --}}
    $.ajax({
        type        : 'POST',
        url         : $( this ).attr( 'action' ),
        data        : location_data,
        dataType    : 'json',
        {{-- Send CSRF Token over ajax --}}
        headers: { 'X-CSRF-TOKEN': Laravel.csrfToken },
        success: function() {

          $('#selectlocation').slideUp('fast', function(){
              $('#savedlocation').slideDown('fast');
          });

          $('#selectlocationfooter').slideUp('fast', function(){
              $('#savedlocationfooter').slideDown('fast');
          });
          @if($force)
          setTimeout(function() {$('#modal_user_location').modal('hide');}, 10000);
          @else
          setTimeout(function() {window.location.href = window.location.href;}, 10000);
          @endif

          {{-- Start counter for closing modal --}}
          c();

        },
        error: function() {
          $('#selectedlocation_panel').slideUp('fast');
          status.removeClass('bg-dark').removeClass('bg-success').addClass('bg-danger');
          status.html('<i class="fa fa-minus-circle" aria-hidden="true"></i> {{ trans('users.modal_location.error') }}');
            $("#postalcode").val("");
            locality.attr('disabled', 'disabled');
            locality.empty().append('<option selected="selected" value="whatever">&larr; {{ trans('users.modal_location.placeholder.postal_code_locality') }}</option>');
            $('[type="submit"]').prop('disabled', true);
        }
    });

    // stop the form from submitting the normal way and refreshing the page
    event.preventDefault();
  });



});
</script>
{{-- END LOCATION CHECK SCRIPT --}}
