<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace modules\embeddedmedia\models;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\base\Exception;
use humhub\models\Setting;
use humhub\modules\embeddedmedia\libs\ImageConverter;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentAddonActiveRecord;

/**
 * This is the model class for table "embeddedmedia".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property string $guid
 * @property string $file_name
 * @property string $title
 * @property string $mime_type
 * @property string $size
 * @property string $object_model
 * @property integer $object_id
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @package humhub.modules_core.embeddedmedia.models
 * @since 0.5
 */
class EmbeddedMedia extends \humhub\components\ActiveRecord
{

    // Configuration
    protected $folder_uploads = "embeddedmedia";

    /**
     * Uploaded Media or Media Content
     *
     * @var UploadedMedia
     */
    private $uploadedMedia = null;

    /**
     * New content of the media
     *
     * @var string
     */
    public $newMediaContent = null;

    /**
     * Returns all embedded media belonging to a given HActiveRecord Object.
     * @todo Add chaching
     *
     * @param HActiveRecord $object
     * @return Array of File instances
     */
    public static function getFilesOfObject(\yii\db\ActiveRecord $object)
    {
        return self::findAll(array('object_id' => $object->getPrimaryKey(), 'object_model' => $object->className()));
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(['created_by', 'updated_by', 'size'], 'integer'),
            array(['guid'], 'string', 'max' => 45),
            array(['mime_type'], 'string', 'max' => 150),
            array('filename', 'validateExtension'),
            array('filename', 'validateSize'),
            array('mime_type', 'match', 'not' => true, 'pattern' => '/[^a-zA-Z0-9\.ä\/\-]/', 'message' => Yii::t('EmbeddedMediaModule.models_Media', 'Invalid Mime-Type')),
            array(['file_name', 'title'], 'string', 'max' => 255),
            array(['created_at', 'updated_at'], 'safe'),
        );
    }

    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\PolymorphicRelation::className(),
                'mustBeInstanceOf' => array(\humhub\components\ActiveRecord::className()),
            ],
            [
                'class' => \humhub\components\behaviors\GUID::className(),
            ],
        ];
    }

    public function beforeSave($insert)
    {
        $this->sanitizeFilename();

        if ($this->title == "") {
            $this->title = $this->file_name;
        }

        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        $path = $this->getPath();

        // Make really sure, that we dont delete something else :-)
        if ($this->guid != "" && $this->folder_uploads != "" && is_dir($path)) {
            $files = glob($path . DIRECTORY_SEPARATOR . "*");
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($path);
        }

        return parent::beforeDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        // Set new uploaded file
        if ($this->uploadedFile !== null && $this->uploadedFile instanceof UploadedFile) {
            $newFilename = $this->getPath() . DIRECTORY_SEPARATOR . $this->getFilename();

            if (is_uploaded_file($this->uploadedFile->tempName)) {
                move_uploaded_file($this->uploadedFile->tempName, $newFilename);
                @chmod($newFilename, 0744);
            }

        }

        // Set file by given contents
        if ($this->newFileContent != null) {
            $newFilename = $this->getPath() . DIRECTORY_SEPARATOR . $this->getFilename();
            file_put_contents($newFilename, $this->newFileContent);
            @chmod($newFilename, 0744);
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'ID'),
            'guid' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Guid'),
            'file_name' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'File name'),
            'title' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Title'),
            'mime_type' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Mime Type'),
            'size' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Size'),
            'created_at' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Created at'),
            'created_by' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Created By'),
            'updated_at' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Updated at'),
            'updated_by' => Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Updated by'),
        );
    }

    /**
     * Returns the Path of the Embedded Media
     */
    public function getPath()
    {
        $path = Yii::getAlias('@webroot') .
                DIRECTORY_SEPARATOR . "uploads" .
                DIRECTORY_SEPARATOR . $this->folder_uploads .
                DIRECTORY_SEPARATOR . $this->guid;

        if (!is_dir($path)) {
            mkdir($path);
        }

        return $path;
    }

    /**
     * Returns the Url of the Embedded Media
     *
     * @param string $suffix
     * @param boolean $absolute
     * @return string
     */
    public function getUrl($suffix = "", $absolute = true)
    {
        $params = array();
        $params['guid'] = $this->guid;
        if ($suffix) {
            $params['suffix'] = $suffix;
        }

        array_unshift($params, '/embedded/embedded/download');

        return Url::to($params, $absolute);
    }

    /**
     * Returns the filename
     *
     * @param string $prefix
     * @return string
     */
    public function getFilename($prefix = "")
    {
        // without prefix
        if ($prefix == "") {
            return $this->file_name;
        }

        $fileParts = pathinfo($this->file_name);

        return $fileParts['filename'] . "_" . $prefix . "." . $fileParts['extension'];
    }

    public function getMimeBaseType()
    {
        if ($this->mime_type != "") {
            list($baseType, $subType) = explode('/', $this->mime_type);
            return $baseType;
        }

        return "";
    }

    public function getMimeSubType()
    {
        if ($this->mime_type != "") {
            list($baseType, $subType) = explode('/', $this->mime_type);
            return $subType;
        }

        return "";
    }

    public function getPreviewImageUrl($maxWidth = 1000, $maxHeight = 1000)
    {

    }

    public function getExtension()
    {
        $fileParts = pathinfo($this->file_name);
        if (isset($fileParts['extension'])) {
            return $fileParts['extension'];
        }
        return '';
    }

    /**
     * Checks if given embedded media can read.
     *
     * If the file is not an instance of HActiveRecordContent or HActiveRecordContentAddon
     * the file is readable for all.
     */
    public function canRead($userId = "")
    {
        $object = $this->getPolymorphicRelation();
        if ($object !== null && ($object instanceof ContentActiveRecord || $object instanceof ContentAddonActiveRecord)) {
            return $object->content->canRead($userId);
        }

        return true;
    }

    /**
     * Checks if given embedded media can deleted.
     *
     * If the file is not an instance of HActiveRecordContent or HActiveRecordContentAddon
     * the file is readable for all unless there is method canWrite or canDelete implemented.
     */
    public function canDelete($userId = "")
    {
        $object = $this->getPolymorphicRelation();
        if ($object !== null && ($object instanceof ContentActiveRecord || $object instanceof ContentAddonActiveRecord)) {
            return $object->content->canWrite($userId);
        }

        // Embedded Media is not bound to an object
        if ($object == null) {
            return true;
        }

        return false;
    }

    public function setUploadedFile(UploadedFile $uploadedMedia)
    {
        $this->file_name = $uploadedMedia->name;
        $this->mime_type = $uploadedMedia->type;
        $this->size = $uploadedMedia->size;
        $this->uploadedFile = $uploadedMedia;
    }

    public function sanitizeFilename()
    {
        $this->file_name = trim($this->file_name);
        $this->file_name = preg_replace("/[^a-z0-9_\-s\. ]/i", "", $this->file_name);

        // Ensure max length
        $pathInfo = pathinfo($this->file_name);
        if (strlen($pathInfo['filename']) > 60) {
            $pathInfo['filename'] = substr($pathInfo['filename'], 0, 60);
        }

        $this->file_name = $pathInfo['filename'];

        if ($this->file_name == "") {
            $this->file_name = "Unnamed";
        }

        if (isset($pathInfo['extension']))
            $this->file_name .= "." . trim($pathInfo['extension']);
    }

    public function validateExtension($attribute, $params)
    {
        $allowedExtensions = Setting::GetText('allowedExtensions', 'file');

        if ($allowedExtensions != "") {
            $extension = $this->getExtension();
            $extension = trim(strtolower($extension));

            $allowed = array_map('trim', explode(",", Setting::GetText('allowedExtensions', 'file')));

            if (!in_array($extension, $allowed)) {
                $this->addError($attribute, Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'This file type is not allowed!'));
            }
        }
    }

    public function validateSize($attribute, $params)
    {
        if ($this->size > Setting::Get('maxFileSize', 'file')) {
            $this->addError($attribute, Yii::t('EmbeddedMediaModule.models_EmbeddedMedia', 'Maximum file size ({maxFileSize}) has been exceeded!', array("{maxFileSize}" => Yii::$app->formatter->asSize(Setting::Get('maxFileSize', 'file')))));
        }
    }

    /**
     * Attaches a given list of files to an record (HActiveRecord).
     * This is used when uploading files before the record is created yet.
     *
     * @param HActiveRecord $object is a HActiveRecord
     * @param string $files is a comma seperated list of newly uploaded file guids
     */
    public static function attachPrecreated($object, $files)
    {
        if (!$object instanceof \yii\db\ActiveRecord) {
            throw new Exception("Invalid object given - require instance of HActiveRecord!");
        }

        // Attach Files
        foreach (explode(",", $files) as $fileGuid) {
            $file = self::findOne(['guid' => trim($fileGuid)]);
            if ($file != null && $file->object_model == "") {
                $file->object_model = $object->className();
                $file->object_id = $object->getPrimaryKey();
                if (!$file->save()) {
                    throw new Exception("Could not save precreated file!");
                }
            }
        }
    }

    public function getInfoArray()
    {
        $info = [];

        $info['error'] = false;
        $info['guid'] = $this->guid;
        $info['name'] = $this->file_name;
        $info['title'] = $this->title;
        $info['size'] = $this->size;
        $info['mimeIcon'] = \humhub\libs\MimeHelper::getMimeIconClassByExtension($this->getExtension());
        $info['mimeBaseType'] = $this->getMimeBaseType();
        $info['mimeSubType'] = $this->getMimeSubType();
        $info['url'] = $this->getUrl("", false);
        $info['thumbnailUrl'] = $this->getPreviewImageUrl(200, 200);

        return $info;
    }

}
