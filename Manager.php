<?php

namespace artkost\attachment;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class Manager extends Component
{
    /**
     * Parameter passed when upload file
     */
    const PARAM_NAME = 'file';

    /**
     * Path of storage in web
     * @var string
     */
    public $storageUrl = '@web/storage';

    /**
     * Path of storage in filesystem
     * @var string
     */
    public $storagePath = '@webroot/storage';

    /**
     * Temp folder for temporary files
     * @var string
     */
    public $tempPath = '@webroot/storage/temp';

    /**
     * @var string
     */
    public $attachmentFileTable = '{{%attachment_file}}';

    /**
     * Instantiated AttachmentFile attributes
     */
    protected $modelsInstances = [];

    public function init()
    {
        parent::init();

        $this->createDirectory($this->storagePath);
        $this->createDirectory($this->tempPath);
    }

    /**
     * Ensure or create a folder
     * @param $path
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function createDirectory($path)
    {
        if (!FileHelper::createDirectory($path)) {
            throw new InvalidConfigException("Directory {$path} doesn't exist or cannot be created.");
        }
    }

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
     *
     * The translation will be conducted according to the message category and the target language will be used.
     *
     * You can add parameters to a translation message that will be substituted with the corresponding value after
     * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
     *
     * ```php
     * $username = 'Alexander';
     * echo \Yii::t('app', 'Hello, {username}!', ['username' => $username]);
     * ```
     *
     * Further formatting of message parameters is supported using the [PHP intl extensions](http://www.php.net/manual/en/intro.intl.php)
     * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     *
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('attachment/' . $category, $message, $params, $language);
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        return Yii::$app->attachment;
    }

    /**
     * @return UploadedFile
     */
    public static function getUploadedFile()
    {
        return UploadedFile::getInstanceByName(self::PARAM_NAME);
    }

    /**
     * @return UploadedFile[]
     */
    public static function getUploadedFiles()
    {
        return UploadedFile::getInstancesByName(self::PARAM_NAME);
    }

    public function addAttachmentModel($ownerClass, $attribute, $config)
    {
        $name = $ownerClass . $attribute;

        return $this->modelsInstances[$name] = Yii::createObject($config);
    }

    public function getAttachmentModel($ownerClass, $attribute)
    {
        $name = $ownerClass . $attribute;

        if (!isset($this->modelsInstances[$name])) {
            //try to create model that attaches AttachBehavior
            Yii::createObject(['class' => $ownerClass]);

            if (!isset($this->modelsInstances[$name])) {
                return null;
            }
        }

        return $this->modelsInstances[$name];
    }

    /**
     * @return string
     */
    public function getStorageUrl()
    {
        return FileHelper::normalizePath(Yii::getAlias($this->storageUrl), '/') . '/';
    }

    /**
     * @return string
     */
    public function getStoragePath()
    {
        return FileHelper::normalizePath(Yii::getAlias($this->storagePath)) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getTempPath()
    {
        return FileHelper::normalizePath(Yii::getAlias($this->tempPath)) . DIRECTORY_SEPARATOR;
    }
} 
