<script>
    var BibleSuperSearchAPIURL = '<?php echo $url; ?>';
</script>

<form action='<?php echo $url ?>/api/download' method='POST' id='bible_download_form'>
    <div style='float:left; width: 40%'>
        <h2>Select Bible(s)</h2>
        Some Bibles are not available due to copyright restrictions. <br /><br />

        <table class='parameters' cellspacing="0">
            <tr>
                <th>&nbsp;</th>
                <th>Name</th>
                <th>Language</th>
                <?php if($verbose): ?><th>Year</th><?php endif; ?>
                <?php if($verbose): ?><th>Downloadable</th><?php endif; ?>
            </tr>

            <?php foreach($bibles as $bible) : ?>
                <tr class='<?php if(!$bible['downloadable']) { echo 'download_disabled'; }?>'>
                    <td>
                        <?php if($bible['downloadable']): ?>
                            <input type='checkbox' name='bible[]' value='<?php echo $bible['module'] ?>' id='bible_download_<?php echo $bible['module'] ?>' class='bible_download_select' />
                        <?php elseif ($verbose): ?>
                            <input type='checkbox' name='bible_null[]' value='1' disabled="disabled" />
                        <?php endif; ?>
                    </td>

                    <td><label for='bible_download_<?php echo $bible['module'] ?>'><?php echo $bible['name'] ?></label></td>
                    <td><?php echo $bible['lang'] ?></td>
                    <?php if($verbose): ?><td><?php echo $bible['year'] ?></td><?php endif; ?>
                    <?php if($verbose): ?><td><?php echo $bible['downloadable'] ? 'Yes' : 'No' ?></td><?php endif; ?>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>
    <div style='float:left; width: 40%; margin-left: 100px'>
        <h2>Select a Format</h2>

        <?php foreach($formats as $kind => $info) : ?>
            <div class='format_group_box'>
                <h2 class='name'><?php echo $info['name']; ?></h2>

                <?php if($info['desc']): ?>
                    <div class='desc'><?php echo $info['desc']; ?></div>
                <?php endif; ?>

                <?php foreach($info['renderers'] as $fmt => $format) : ?>
                    <div class='format_box'>
                        <input type='radio' name='format' value='<?php echo $fmt; ?>' id='format_<?php echo $fmt; ?>' />
                        <label class='format_name' for='format_<?php echo $fmt; ?>'><?php echo $format['name']; ?></label> <?php echo $format['desc']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div style='clear:both'></div>

    <div class='center' style='width: 100px'>
        <input id='bible_download_submit' type='submit' value='Download' class='button' />
    </div>
</form>

<div class='pseudo_dialog' id='bible_download_dialog'>
    <div class='container' style='width:600px'>
        <div class='contents' id='bible_download_dialog_content' style='height:200px'>
            
        </div>
        <div class='buttons'>
            <button id='render_cancel'>Cancel</button>
        </div>
    </div>
</div>