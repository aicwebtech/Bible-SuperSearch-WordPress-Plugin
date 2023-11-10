jQuery(document).ready(function($) { 

    $('#biblesupersearch_all_bibles').click(function() {
        changeAllBibles();
    });

    $('#biblesupersearch_check_all_bibles').click(function() {
        $('.bss_bible input[type=checkbox]').prop('checked', true);
    });    

    $('#biblesupersearch_uncheck_all_bibles').click(function() {
        $('.bss_bible input[type=checkbox]').prop('checked', false);
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
                var valid = true,
                    version = false;

                if(!data.results || !data.results.name || !data.results.version || data.error_level != 0 || !Array.isArray(data.errors)) {
                    valid = false;
                }
                else {
                    version = parseFloat(data.results.version);

                    // Perhaps there is a better way
                    if(version >= 5 && data.results.hash != 'fd9f996adfe0beb419a5a40b2adaf573baf55464f7c2c9101b4d7ce6e42310cf') {
                        valid = false;
                    }
                }

                if(!valid) {
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
            $('.biblesupersearch_toggled_bible').hide();
        }
        else {
            $('.biblesupersearch_toggled_bible').show();
        }
    }

    $('#biblesupersearch_def_bible_add').click(function(e) {
        e.preventDefault();
        addDefaultBible();
        return true;
    });    

    $('#biblesupersearch_def_bible_rem').click(function(e) {
        e.preventDefault();
        removeDefaultBible();
        return true;
    });

    if(bss_options.defaultBible.length < 2) {
        jQuery('#biblesupersearch_def_bible_rem').hide();
    }

    changeAllBibles();

    if(bss_tab == 'bible') {
        $('.parallelBibleLimitByWidthAdd').click(function(e) {
            parallelBibleLimitAdd(e);
        });    

        $('.parallelBibleLimitByWidthRemove').click(function(e) {
            parallelBibleLimitRemove(e);
        });

        $('#parallelBibleLimitByWidthEnable').click(function(e) {

            if($(this).prop('checked')) {
                $('#parallelBibleLimitByWidthContainer').show();
                parallelBibleLimitAdd(null, null);
            } else if(confirm('Are you sure?  This will delete all of the parallel limit settings!')) {
                $('#parallelBibleLimitByWidthContainer').hide();
                parallelBibleLimitClear();
            }
        });

        parallelBibleLimitInit();
    }

    $('input[type=submit]').click(function(e) {
        var errors = false;

        if(bss_tab == 'bible') {
            if(!parallelBibleLimitValidate(1)) {
                errors = true;
            }
        }

        errors && e.preventDefault();
    });
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


function addDefaultBible(e) {
    e && e.preventDefault();  
    pb = jQuery('#defaultBibleContainer select').length + 1;
    innerHtml = jQuery('#default_bible_0').html();
    innerHtml = innerHtml.replace('selected="selected"','');
    innerHtml = innerHtml.replace("selected='selected'",'');
    innerHtml = "<option value='0'>Parallel Bible " + pb + " - None</option>" + innerHtml;

    html = "<select name='biblesupersearch_options[defaultBible][]'>" + innerHtml + "</select>";
    jQuery('#biblesupersearch_def_bible_rem').show();

    jQuery('#defaultBibleContainer').append(html);

    return true;
}

function removeDefaultBible(e) {
    e && e.preventDefault();  
    pbs = jQuery('#defaultBibleContainer select');

    if(pbs.length < 3) {
        jQuery('#biblesupersearch_def_bible_rem').hide();
    }

    if(pbs.length == 1) {
        return;
    }

    pb = pbs.last();
    pb.remove();
}

function parallelBibleLimitInit() {
    //e && e.preventDefault();  

    console.log('bssParBibleLimit', bssParBibleLimit);

    var dec = (typeof bssParBibleLimit == 'string') ? JSON.parse(bssParBibleLimit) : bssParBibleLimit;
    // var dec = JSON.parse(bssParBibleLimit);

    console.log('bssParBibleLimit decoded', dec);

    if(dec.length > 0) {
        jQuery('#parallelBibleLimitByWidthEnable').prop('checked', true);
        jQuery('#parallelBibleLimitByWidthContainer').show();

        dec.forEach(function(item) {
            parallelBibleLimitAdd(null, item);
        });
    }

    parallelBibleLimitValidate(3);
}

function parallelBibleLimitAdd(e, rowData) {
    e && e.preventDefault();  

    var count = parallelBibleLimitNumberShowing();
    var rPre = "<td><input name='biblesupersearch_options[parallelBibleLimitByWidth][";
    var rPost = "]'></td>";

    var maxBiblesDefault = count == 0 ? 1 : '';

    var minWidth = rowData && rowData.minWidth ? rowData.minWidth : '';
    var maxWidth = rowData && rowData.maxWidth ? rowData.maxWidth : '(infinite)';
    var maxBibles = rowData && rowData.maxBibles ? rowData.maxBibles : maxBiblesDefault;
    var minBibles = rowData && rowData.minBibles ? rowData.minBibles : 1;
    var startBibles = rowData && rowData.startBibles ? rowData.startBibles : 1;

    var html = "<tr>";
        
    if(count == 0) {
        html += rPre + count + "][minWidth]' value='0' readonly='readonly'></td>";
    } else {
        html += rPre + count + "][minWidth]' value='" + minWidth + "' class='bssMinWidth'></td>";
    }

    html += "<td><input value='" + maxWidth + "' readonly='readonly'></td>";
    html += rPre + count + "][maxBibles]' value='" + maxBibles + "'></td>";
    html += rPre + count + "][minBibles]' value='" + minBibles + "'></td>";
    html += rPre + count + "][startBibles]' value='" + startBibles + "'></td>";
    html += "</tr>";

    var lastRowInputs = jQuery('#parallelBibleLimitByWidthTbody tr').last().find('input'); 
    jQuery(lastRowInputs[1]).val('');
    jQuery('#parallelBibleLimitByWidthTbody').append(html);

    jQuery('.bssMinWidth').change(function() {
        parallelBibleLimitValidate(3);
    })
}

function parallelBibleLimitRemove(e) {
    e && e.preventDefault(); 

    if(parallelBibleLimitNumberShowing() > 0) {
        jQuery('#parallelBibleLimitByWidthTbody tr').last().remove();
        var lastRowInputs = jQuery('#parallelBibleLimitByWidthTbody tr').last().find('input'); 
        jQuery(lastRowInputs[1]).val('(infinite)');
    }
}

function parallelBibleLimitNumberShowing() {
    return jQuery('#parallelBibleLimitByWidthTbody tr').length;
}

function parallelBibleLimitClear() {
    jQuery('#parallelBibleLimitByWidthTbody').html('');
}

function parallelBibleLimitValidate(level) {
    var count = 0,
        curMinWidth = 0,
        valid = true,
        errors = [],
        // 1=all validation, 2=lazy validation, 3=maximum width ONLY

        level = typeof level == 'undefind' ? 1 : level,
        lazy = level > 1,
        lastRow = null;

        errorsByName = {
            minWidthAsc: false,
            maxBiblesPosInt: false,
            minBiblesPosInt: false,
            startBiblesPosInt: false,
            startBiblesGEMinBibles: false,
        };

    jQuery('#parallelBibleLimitByWidthTbody tr').each(function(row) {
        var inputs = jQuery(this).find('input');        
        var minWidth = parseInt(jQuery(inputs[0]).val(), 10);
        var maxBibles = parseInt(jQuery(inputs[2]).val(), 10);
        var minBibles = parseInt(jQuery(inputs[3]).val(), 10);
        var startBibles = parseInt(jQuery(inputs[4]).val(), 10);

        jQuery(inputs).removeClass('error');

        minWidth = !minWidth || minWidth == NaN ? 0 : minWidth;
        maxBibles = !maxBibles || maxBibles == NaN ? 0 : maxBibles;
        minBibles = !minBibles || minBibles == NaN ? 0 : minBibles;
        startBibles = !startBibles || startBibles == NaN ? 0 : startBibles;

        if(count > 0) {
            if(minWidth <= curMinWidth ) {
                if(level < 3) {
                    errorsByName.minWidthAsc = true;
                    valid = false;
                    jQuery(inputs[0]).addClass('error');
                }
            } else {
                jQuery(inputs[0]).val(minWidth);

                if(lastRow) {
                    jQuery(lastRow[1]).val(minWidth - 1);
                }
            }

            curMinWidth = minWidth;
        }

        if(level < 3) {        
            if(maxBibles < 1) {
                valid = false;
                errorsByName.maxBiblesPosInt = true;
                jQuery(inputs[2]).addClass('error');
            } else {
                jQuery(inputs[2]).val(maxBibles);
            }        

            if(minBibles < 1) {
                valid = false;
                errorsByName.minBiblesPosInt = true;
                jQuery(inputs[3]).addClass('error');
            } else {
                jQuery(inputs[3]).val(minBibles);
            }
            
            if(startBibles < minBibles) {
                valid = false;
                errorsByName.startBiblesGEMinBibles = true;
                jQuery(inputs[4]).addClass('error');
            } else if(startBibles < 1) {
                valid = false;
                errorsByName.startBiblesPosInt = true;
                jQuery(inputs[4]).addClass('error');
            } else {
                jQuery(inputs[4]).val(startBibles);
            } 
        }

        count ++;
        lastRow = inputs;
    });

    if(!valid && !lazy) {
        if(errorsByName.minWidthAsc) {
            errors.push('Parallel Limit: Minimum width must be in ascending order.');
        }

        if(errorsByName.maxBiblesPosInt) {
            errors.push('Parallel Limit: Maximum Bibles must be a positive integer');
        }        

        if(errorsByName.minBiblesPosInt) {
            errors.push('Parallel Limit: Minimum Bibles must be a positive integer');
        }        

        if(errorsByName.startBiblesPosInt) {
            errors.push('Parallel Limit: Page Load Bibles must be a positive integer');
        }        

        if(errorsByName.startBiblesGEMinBibles) {
            errors.push('Parallel Limit: Page Load Bibles must be greater or equal to Minimum Bibles');
        }

        alert(errors.join('\n'));
    }

    return valid;
}