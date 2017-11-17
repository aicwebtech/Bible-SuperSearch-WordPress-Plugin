jQuery(document).ready(function($) { 

    $('#biblesupersearch_all_bibles').click(function() {
        changeAllBibles();
    });

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
    // $('#biblesupersearch_all_bibles').trigger('click');
});
