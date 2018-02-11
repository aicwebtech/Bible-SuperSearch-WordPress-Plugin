jQuery(document).ready(function($) { 

    $('#biblesupersearch_all_bibles').click(function() {
        changeAllBibles();
    });

    $('#biblesupersearch_url').change(function(e) {
        var url = $(this).val(),
            that = this,
            orig = e.currentTarget.defaultValue;

        if(url == '') {
            $(that).css('background-color', '#8be088');
            return;
        }

        // console.log('url', url, orig, e);
        $(that).css('background-color', 'transparent');
        $('.button-primary').prop('disabled', true);

        $.ajax({
            dataType: "json",
            url: url + '/api/version',
            method: 'POST',
            success: function(data) {
                if(!data.results || !data.results.name || data.results.name != 'Bible SuperSearch API') {
                    alert('Error:\nURL \'' + url + '\' does not appear to be an instance of \nthe Bible SuperSearch API, reverting to original.');
                    $(that).val(orig);
                    $('.button-primary').prop('disabled', false);
                }
                else {
                    $(that).css('background-color', '#8be088');
                    $('.button-primary').prop('disabled', false);
                }
            },
            error: function(data) {
                alert('Error:\nCannot load URL \'' + url + '\',\nreverting to original.');
                $(that).val(orig);
            }
        });
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
});
