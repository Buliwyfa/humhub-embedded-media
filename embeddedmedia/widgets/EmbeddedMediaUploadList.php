<?php

namespace modules\embeddedmedia\widgets;

/**
 * FileUploadListWidget works in combination of FileUploadButtonWidget and is
 * primary responsible to display some status informations like upload progress
 * or a list of already uploaded files.
 *
 * This widget cannot work standalone! Make sure the attribute "uploaderId" is
 * the same as the corresponding FileUploadListWidget.
 *
 * @package humhub.modules_core.file.widgets
 * @since 0.5
 */
class EmbeddedMediaUploadList extends \yii\base\Widget
{

    /**
     * @var String unique id of this uploader
     */
    public $uploaderId = "";

    /**
     * If object is set, display also already uploaded files
     *
     * @var HActiveRecord
     */
    public $object = null;

    /**
     * Draw the widget
     */
    public function run()
    {

        $files = array();
        if ($this->object !== null) {
            $files = modules\embeddedmedia\models\EmbeddedMedia::getFilesOfObject($this->object);
        }

        return $this->render('embeddedMediaUploadList', array(
                    'uploaderId' => $this->uploaderId,
                    'files' => $files
        ));
    }

}

?>
