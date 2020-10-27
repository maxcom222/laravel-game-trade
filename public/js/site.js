$(function () {
  var mediaQuery = "(-webkit-min-device-pixel-ratio: 1.5),\
          (min--moz-device-pixel-ratio: 1.5),\
          (-o-min-device-pixel-ratio: 3/2),\
          (min-device-pixel-ratio: 3/2),\
          (min-resolution: 1.5dppx),\
          (min-resolution: 192dpi)";
  if ((window.matchMedia && window.matchMedia(mediaQuery).matches) || window.devicePixelRatio > 1) {
    var images = $("img.hires");

    // loop through the images and make them hi-res
    for(var i = 0; i < images.length; i++) {

      // create new image name
      var imageType = images[i].src.substr(-4);
      var imageName = images[i].src.substr(0, images[i].src.length - 4);
      imageName += "@2x" + imageType;

      //rename image
      images[i].src = imageName;
    }
  }

});

$(document).ready(function(){
  $("div.lazy").lazyload({
      effect : "fadeIn",
      load : function(elements_left, settings) {
        $(this).parent().parent().find('.pacman-loader').delay(200).fadeOut();
      }
  });
});
