var bibleDownloadGo = false;
var bibleRenderQueue = [];
var bibleRenderQueueProcess = false;
var bibleRenderSelectedFormat = null;

if(!jQuery) {
    alert('jQuery is required!');
}

if(!$) {
    $ = jQuery; // Because WordPress uses an archaic version of jQuery
}

$( function() {

    $('#bible_download_submit').click(function(e) {
        console.log('hahaha');
        bibleRenderSelectedFormat = null;

        var hasBibles = false,
            hasFormat = false;

        $('input[name="bible[]"]').each(function() {
            if( ($(this).prop('checked') )) {
                hasBibles = true;
            }
        });

        $('input[name=format]').each(function() {
            if( ($(this).prop('checked') )) {
                hasFormat = true;
                bibleRenderSelectedFormat = $(this).val();
            }
        });

        console.log(hasBibles, hasFormat);

        var err = '';

        if(!hasBibles) {
            // err += 'Please select at least one Bible. \n';
            err += 'Please select at least one Bible. <br>';
        }

        if(!hasFormat) {
            err += 'Please select a format.';
        }

        if(!hasBibles || !hasFormat) {
            // alert(err);
            bibleDownloadAlert(err);
            e.preventDefault();
            return false;
        }

        $.ajax({
            url: BibleSuperSearchAPIURL + '/api/render',
            data: $('#bible_download_form').serialize(),
            dataType: 'json',
            success: function(data, status, xhr) {
                console.log('success', data);
                $('#bible_download_form').submit();

            },
            error: function(xhr, status, error) {
                console.log('error', xhr);
                var response = JSON.parse(xhr.responseText);
                console.log(response);
                
                if(response.results.separate_process_supported) {
                    bibleDownloadAlert(response.errors.join('<br>'));
                }
                else {
                    bibleDownloadInitProcess();
                }
            }
        });

            e.preventDefault();
            return false;
    });

    $('#render_cancel').click(function() {
        $('#bible_download_dialog').hide();
        bibleRenderQueueProcess = false;
    });
});

function bibleDownloadError(text) {

}

function bibleDownloadAlert(text) {
    $('#bible_download_dialog_content').html(text);
    $('#bible_download_dialog').show();
}

function bibleDownloadInitProcess() {
    bibleRenderQueueProcess = true;

    bibleRenderQueue = [];

    $('.bible_download_select:checkbox:checked').each(function(i) {
        bibleRenderQueue.push( $(this).val() );
    });

    console.log(bibleRenderQueue);

    bibleDownloadAlert('<h2>Rendering Bibles, this may take a while</h2><br><br>');
    bibleDownloadProcessNext();
}

function bibleDownloadProcessNext() {
    if(bibleRenderQueueProcess) {
        var bible = bibleRenderQueue.pop();

        var name = $('label[for="bible_download_' + bible +'"]').html();

        var text = 'Rendering ' + name + ' ...';

        $('#bible_download_dialog_content').append(text);

        $.ajax({
            url: BibleSuperSearchAPIURL + '/api/render',
            data: {bible: bible, format: bibleRenderSelectedFormat},
            dataType: 'json',
            success: function(data, status, xhr) {
                console.log('success', data);
                $('#bible_download_dialog_content').append(' done<br>');

                if(bibleRenderQueue.length == 0) {
                    bibleDownloadProcessFinal();
                }
                else {
                    bibleDownloadProcessNext();
                }
            },
            error: function(xhr, status, error) {
                console.log('error', xhr);
                var response = JSON.parse(xhr.responseText);
                console.log(response);
                
            }
        });
    }
}

function bibleDownloadProcessFinal() {
    alert('done');
}