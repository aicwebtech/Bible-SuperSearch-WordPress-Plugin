// if(typeof $ == 'undefined') {
//     var $ = jQuery; // causing breakage and not being used, commenting out for now
// }


jQuery(document).ready(function() {
    // setUpContactForm7();

    //jQuery('.biblesupersearch_dup_shortcode').show();
})


function setUpContactForm7() {
    if(typeof biblesupersearch_cf7id == 'undefined' || !biblesupersearch_cf7id) {
        return;
    }
    
    // alert('here');
    var idPart = 'wpcf7-f' + biblesupersearch_cf7id;
    $('div[id^="' + idPart + '"] .wpcf7-response-output').remove();

    $('div[id^="' + idPart + '"]').on('wpcf7submit', function(e) {
        var response = e.originalEvent.detail.apiResponse;

        delete response.message;

        $.ajax({
            url: biblesupersearch_config_options.apiUrl + 'api',
            method: 'GET',
            // data: e.detail.formData,
            data: $('div[id^="' + idPart + '"] form').serialize(),

            success: function(response, textStatus, xhr ) {
                console.log('response', response);
                window.location.hash = '#/c/' + response.hash;
            },
            error: function(xhr, status, error) {
                console.log(xhr);

                var response = xhr.responseJSON;

                console.log('response', response);
                window.location.hash = '#/c/' + response.hash + '/1';
            }
        })

        console.log(response);

        // alert('submit');
        console.log('e', e);

        e.preventDefault();
        return true;
    })

    // $('.wpcf7').on('wpcf7submit', function() {
    //     alert('w submit');
    // })
}