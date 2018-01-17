  // Uploading files
var file_frame;

  jQuery('#upload_image_button').live('click', function( event ){

    event.preventDefault();

    // If the media frame already exists, reopen it.
    if ( file_frame ) {
      file_frame.open();
      return;
    }

    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
      title: jQuery( this ).data( 'uploader_title' ),
      button: {
        text: jQuery( this ).data( 'uploader_button_text' ),
      },
      multiple: false  // Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on( 'select', function() {
      // We set multiple to false so only get one image from the uploader
      attachment = file_frame.state().get('selection').first().toJSON();
      
      jQuery('#ddz_logo').val(attachment.id);
      jQuery("#ddz_img_url").attr('src', attachment.url);

      
    });

    // Finally, open the modal
    file_frame.open();
  });



// external js: isotope.pkgd.js


$( function () {

  var $container = $('#coupon-loop');

  $container.isotope({})

  $('#filter-select').change( function() {
    $container.isotope({
      filter: this.value
    });
  });

});



  // Modal javascript

// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 


// When the user clicks on <span> (x), close the modal
// span.onclick = function() {
//     modal.style.display = "none";
// }

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}



function copyToClipboard(element) {
  var $temp = jQuery("<input>");
  jQuery("body").append($temp);
  $temp.val(jQuery(element).text()).select();
  document.execCommand("copy");
  $temp.remove();
  jQuery("#coupon_btn").text("COPIED").css({"background-color": "green"});

}


