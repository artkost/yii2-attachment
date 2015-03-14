<?php

namespace artkost\attachment\models;

use artkost\attachment\Manager;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidCallException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;

/**
 * This is the model class for table "attachment_file".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $uri
 * @property string $mime
 * @property integer $size
 * @property string $type
 * @property integer $status_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string dirPath
 * @property string dirUrl
 * @property string filePath
 * @property string fileUrl
 *
 */
class AttachmentFile extends ActiveRecord
{
    /**
     * Temporary files are removed after period of time
     */
    const STATUS_TEMPORARY = 0;
    /**
     *
     */
    const STATUS_PERMANENT = 1;

    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * Path joined with '@storage' alias
     * @var string
     */
    public $path = 'uploads';

    /**
     * Attribute of model this file belongs
     * @var string
     */
    public $attribute;

    /**
     * Unique name of file or not
     * @var
     */
    public $unique;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%attachment_file}}';
    }

    /**
     * Type of attachment
     * @return string
     */
    public static function type()
    {
        return 'default';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'uri', 'mime', 'size'], 'required'],
            [['user_id', 'size', 'created_at', 'updated_at'], 'integer'],
            [['name', 'uri', 'mime', 'type'], 'string', 'max' => 255],
            [['file'], 'file'],
            [['status_id'], 'in', 'range' => array_keys(self::statusLabels())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Manager::t('model','ID'),
            'user_id' => Manager::t('model','User ID'),
            'name' => Manager::t('model','Name'),
            'uri' => Manager::t('model','Uri'),
            'mime' => Manager::t('model','Mime'),
            'size' => Manager::t('model','Size'),
            'type' => Manager::t('model','Type'),
            'created_at' => Manager::t('model','Created At'),
            'updated_at' => Manager::t('model','Updated At'),
        ];
    }

    /**
     * @return array
     */
    public function statusLabels()
    {
        return [
            self::STATUS_TEMPORARY => Manager::t('model', 'Temporary'),
            self::STATUS_PERMANENT => Manager::t('model', 'Permanent')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestampBehavior' => [
                'class' => TimestampBehavior::className(),
            ]
        ];
    }

    /**
     * @return string
     */
    public function getDirPath()
    {
        return Yii::$app->attachment->getStoragePath() . dirname($this->uri) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getDirUrl()
    {
        return Yii::$app->attachment->getStorageUrl() . dirname($this->uri) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return Yii::$app->attachment->getStoragePath() . $this->uri;
    }

    /**
     * @return string
     */
    public function getFileUrl()
    {
        return Yii::$app->attachment->getStorageUrl() . $this->uri;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return Url::base() . $this->getFileUrl();
    }

    /**
     * @return string
     */
    public function getFileError()
    {
        return $this->getFirstError('file');
    }

    /**
     * Set model attributes from UploadedFile
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
        $this->name = $file->name;

        if ($this->unique === true && $file->extension) {
            $this->setUriName(uniqid() . '.' . $file->extension);
        } else {
            $this->setUriName($this->name);
        }

        if (file_exists($this->filePath)) {
            $this->setUriName($file->baseName . uniqid() . '.' . $file->extension);
        }

        $this->mime = FileHelper::getMimeTypeByExtension($this->name);
        $this->size = $file->size;

        return $this;
    }

    /**
     * Set owner of file
     * @param IdentityInterface $user
     * @return $this
     */
    public function setUser(IdentityInterface $user)
    {
        $this->user_id = $user->getId();

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    protected function setUriName($name)
    {
        $this->uri = FileHelper::normalizePath($this->path . DIRECTORY_SEPARATOR . $name);

        return $this;
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function saveAsPermanent($runValidation = true, $attributeNames = null)
    {
        $this->status_id = self::STATUS_PERMANENT;

        return $this->save($runValidation, $attributeNames);
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function saveAsTemporary($runValidation = true, $attributeNames = null)
    {
        $this->status_id = self::STATUS_TEMPORARY;

        return $this->save($runValidation, $attributeNames);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->type = static::type();

                if (!is_numeric($this->status_id)) {
                    $this->status_id = self::STATUS_TEMPORARY;
                }

                if (!FileHelper::createDirectory($this->getDirPath())) {
                    throw new InvalidCallException("Directory @storage/{$this->path} doesn't exist or cannot be created.");
                }

                if (!$this->file->saveAs($this->getFilePath())) {
                    throw new ErrorException("File @storage/{$this->uri} cannot be saved.");
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        if (is_file($this->filePath)) {
            return unlink($this->filePath);
        }

        return false;
    }
}
