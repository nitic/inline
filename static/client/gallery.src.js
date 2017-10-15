$(document).ready(function(){
   $('.inlinecms-gallery.lightbox .image a').abigimage({
        onopen: function (target) {
            this.bottom.html( $(target).attr('title') );
        }
    });
});
