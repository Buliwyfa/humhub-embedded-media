<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->registerJsVar('embeddedmediauploader_error_modal_title', '<strong>Upload</strong> error');
$this->registerJsVar('embeddedmediauploader_error_modal_btn_close', 'Close');
$this->registerJsVar('embeddedmediauploader_error_modal_errormsg', 'Could not upload File:');
?>

<?php echo Html::hiddenInput($this->context->fileListFieldName, '', array('id' => "fileUploaderHiddenField_" . $uploaderId)); ?>

<style>
    .fileinput-button {
        position: relative;
        overflow: hidden;
    }

    .fileinput-button input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        transform: translate(-300px, 0) scale(4);
        font-size: 23px;
        direction: ltr;
        cursor: pointer;
    }
</style>
<span class="btn btn-info fileinput-button tt" data-toggle="tooltip" data-placement="bottom" title=""
      data-original-title='Upload video'); ?>
    <i class="fa fa-cloud-upload"></i>

    <input id="fileUploaderButton_<?php echo $uploaderId; ?>" type="file" name="files[]"
           data-url="<?php echo Url::to(['/embeddedmedia/embedded-media/upload', 'objectModel' => $objectModel, 'objectId' => $objectId]); ?>" multiple>
</span>

<script>
    $(function () {
        'use strict';
        installUploader("<?php echo $uploaderId; ?>");

        // fixing staying tooltip while opening file browser window
        $('.fileinput-button').click(function () {
            $('.tt').tooltip('hide');
        })
    })

</script>
