@extends('frontend.layouts.app')

@section('content')

  <div class="empty-list">

            <div class="icon">
              <i class="far fa-frown" aria-hidden="true"></i>
            </div>

            <div class="text">
              {{ trans('messenger.no_threads') }}
            </div>
            <a href="javascript:void(0)" data-toggle="modal" data-target="#NewMessage" class="btn btn-primary btn-round "><i class="fas fa-envelope-open m-r-5"></i>{{ trans('messenger.new_message') }}</a>
          </div>

@include('frontend.messenger.partials.modal-message')

@stop
