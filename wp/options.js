jQuery(document).ready(function($) { 

    $('#biblesupersearch_all_bibles').click(function() {
        changeAllBibles();
    });

    $('#biblesupersearch_url').change(function(e) {
        var url = $(this).val().replace(/\/+$/, ''),
            that = this,
            orig = e.currentTarget.defaultValue;

        if(url == '') {
            $(that).css('background-color', '#8be088');
            return;
        }

        $(that).css('background-color', 'transparent');
        $('.button-primary').prop('disabled', true);

        $.ajax({
            dataType: "json",
            url: url + '/api/version',
            method: 'POST',
            success: function(data) {
                if(!data.results || !data.results.name || !data.results.version || data.error_level != 0 || !Array.isArray(data.errors)) {
                    alert('Error:\nURL \'' + url + '\' does not appear to be an instance of \nthe Bible SuperSearch API, reverting to original.');
                    $(that).val(orig);
                    $('.button-primary').prop('disabled', false);
                }
                else {
                    $(that).css('background-color', '#8be088');
                    $(that).val(url);
                    $('.button-primary').prop('disabled', false);
                }
            },
            error: function(data) {
                alert('Error:\nCannot load URL \'' + url + '\',\nreverting to original.');
                $(that).val(orig);
            }
        });
    });

    // $('input[type=color]').wpColorPicker();

    function changeAllBibles() {
        var value = $('#biblesupersearch_all_bibles').prop('checked');

        if(value) {
            $('.biblesupersearch_enabled_bible').hide();
        }
        else {
            $('.biblesupersearch_enabled_bible').show();
        }
    }

    changeAllBibles();
});

jQuery(function($){

  // Set all variables to be used in scope
  var frame,
      metaBox = $('#imagebox'), // Your meta box id here
      addImgLink = metaBox.find('.upload-custom-img'),
      delImgLink = metaBox.find( '.delete-custom-img'),
      imgContainer = metaBox.find( '.custom-img-container'),
      imgIdInput = metaBox.find( '.custom-img-id' );
  
  // ADD IMAGE LINK
  addImgLink.on( 'click', function( event ){
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( frame ) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Select or Upload Media Of Your Chosen Persuasion',
            button: {
                text: 'Use this media'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        // When an image is selected in the media frame...
        frame.on( 'select', function() {

            // Get media attachment details from the frame state
            var attachment = frame.state().get('selection').first().toJSON();

            // Send the attachment URL to our custom image input field.
            imgContainer.append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );

            // Send the attachment id to our hidden input
            imgIdInput.val( attachment.id );

            // Hide the add image link
            addImgLink.addClass( 'hidden' );

            // Unhide the remove image link
            delImgLink.removeClass( 'hidden' );
        });

        // Finally, open the modal on click
        frame.open();
    });
  
  
    // DELETE IMAGE LINK
    delImgLink.on( 'click', function( event ){

        event.preventDefault();

        // Clear out the preview image
        imgContainer.html( '' );

        // Un-hide the add image link
        addImgLink.removeClass( 'hidden' );

        // Hide the delete image link
        delImgLink.addClass( 'hidden' );

        // Delete the image id from the hidden input
        imgIdInput.val( '' );

    });

});

