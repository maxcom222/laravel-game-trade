@if($videos != NULL)

  <div class="owl-carousel owl-video">
    @foreach($videos as $video)
      @if($video->youtube_id !== 0 )
        <div class="overlay hvr-grow-shadow2 b-r">
          <a class="video-gallery" href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" data-effect="mfp-zoom-in">
            <img src="{{ 'https://img.youtube.com/vi/' . $video->youtube_id . '/mqdefault.jpg' }}" alt="{{$video->name}}">
            <div class="imgDescription"><div class="valign"><i class="fa fa-play-circle" aria-hidden="true"></i></div></div>
            <div class="time"><i class="fa fa-clock" aria-hidden="true"></i> {{ gmdate("i:s", $video->length_seconds) }} </div>
            <div class="videoname">{{ $video->name }}</div>
          </a>
        </div>
      @endif
    @endforeach
  </div>

@endif



<div class="grid">

  @foreach($images as $image)
  <div class="grid-item">
      <div class="overlay hvr-grow-shadow3">
        <a class="game-gallery" href="https://static.giantbomb.com/uploads/scale_super/{{ $image->image }}" data-source="https://static.giantbomb.com/uploads/scale_super/{{ $image->image }}" title="{{ $game->name }} {{ $image->tags }}" data-effect="mfp-zoom-in">
            <img class="lazy overlay-figure" src="https://static.giantbomb.com/uploads/scale_small/{{ $image->image }}"
            alt="...">
            <div class="imgDescription"><div class="valign"><i class="fa fa-expand" aria-hidden="true"></i></div></div>
        </a>
      </div>
  </div>
  @endforeach

</div>

<link rel="stylesheet" href="{{ asset('css/magnific-popup.min.css') }}">

{{-- Load css data for popup --}}
<script type="text/javascript">
$(function() {

  {{-- Init masonry --}}
  var $grid = $('.grid').masonry({
    itemSelector: '.grid-item',
    columnWidth: '.grid-item',
    percentPosition: true
  });

  {{-- layout Masonry after each image loads --}}
  $grid.imagesLoaded().progress( function() {
    $grid.masonry('layout');
  });

  {{-- Popup for gallery --}}
  $('.game-gallery').magnificPopup({
      type: 'image',
      tClose: '{{ trans('games.gallery.close') }}',
      tLoading: '{{ trans('games.gallery.loading') }}',
      gallery: {
        tPrev: '{{ trans('games.gallery.prev') }}',
        tNext: '{{ trans('games.gallery.next') }}',
        tCounter: '%curr% {{ trans('games.gallery.counter') }} %total%',
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
      },
      image: {
          tError: '{{ trans('games.gallery.error') }}'
      },
      mainClass: 'mfp-zoom-in',
      removalDelay: 300, //delay removal by X to allow out-animation
      callbacks: {
          beforeOpen: function() {
              $('#portfolio a').each(function(){
                  $(this).attr('title', $(this).find('img').attr('alt'));
              });
          },
          open: function() {
              //overwrite default prev + next function. Add timeout for css3 crossfade animation
              $.magnificPopup.instance.next = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.next.call(self); }, 120);
              };
              $.magnificPopup.instance.prev = function() {
                  var self = this;
                  self.wrap.removeClass('mfp-image-loaded');
                  setTimeout(function() { $.magnificPopup.proto.prev.call(self); }, 120);
              };
          },
          imageLoadComplete: function() {
              var self = this;
              setTimeout(function() { self.wrap.addClass('mfp-image-loaded'); }, 16);
          }
      }
   });

   {{-- Popup for video --}}
   $('.video-gallery').magnificPopup({
       disableOn: 700,
       removalDelay: 160,
       type: 'iframe',
       tClose: '{{ trans('games.gallery.close') }}',
       tLoading: '{{ trans('games.gallery.loading') }}',
       mainClass: 'mfp-zoom-in',
       removalDelay: 300, //delay removal by X to allow out-animation
       callbacks: {
           beforeOpen: function() {
               $('#portfolio a').each(function(){
                   $(this).attr('title', $(this).find('img').attr('alt'));
               });
           },
           open: function() {
               //overwrite default prev + next function. Add timeout for css3 crossfade animation
               $.magnificPopup.instance.next = function() {
                   var self = this;
                   self.wrap.removeClass('mfp-image-loaded');
                   setTimeout(function() { $.magnificPopup.proto.next.call(self); }, 120);
               };
               $.magnificPopup.instance.prev = function() {
                   var self = this;
                   self.wrap.removeClass('mfp-image-loaded');
                   setTimeout(function() { $.magnificPopup.proto.prev.call(self); }, 120);
               };
           },
           imageLoadComplete: function() {
               var self = this;
               setTimeout(function() { self.wrap.addClass('mfp-image-loaded'); }, 16);
           }
       },
       preloader: true
    });

   {{-- Carousel for videos --}}
   $(".owl-carousel").on('initialize.owl.carousel',function(){
       $(".owl-carousel").addClass('carousel-loaded');
   });

  $(".owl-carousel").owlCarousel({
     autoplay: true,
           nav:false,
           dots:false,
           lazyLoad: true,
           loop: false,
           items : 4, //4 items above 1000px browser width
           margin: 20,
           responsive:{
               0:{
                   items:1
               },
               600:{
                   items:2
               },
               900:{
                   items:2
               },
               1100:{
                   items:3
               },
               1500:{
                   items:4
               }
           }
   });

});

</script>
