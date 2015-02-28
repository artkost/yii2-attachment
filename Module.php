<?php

namespace app\modules\attachment;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class Module extends \yii\base\Module
{
    public static $name = 'attachment';

    /**
     * Parameter passed when upload file
     */
    const PARAM_NAME = 'file';

    /**
     * @var string
     */
    public $storageUrl = '@web/storage';
    /**
     * @var string
     */
    public $storagePath = '@webroot/storage';
    /**
     * @var string
     */
    public $tempPath = '@webroot/storage/temp';

    /**
     * @var array Publish path cache array
     */
    protected static $_cachePublishPath = [];

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
        return Yii::t(self::$name . '/' . $category, $message, $params, $language);
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

    /**
     * @param $file
     * @return string
     */
    public function getUrlOfFile($file)
    {
        return $this->getStorageUrl() . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getPathOfFile($file)
    {
        return $this->getStoragePath() . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getTempOfFile($file)
    {
        return $this->getTempPath() . $file;
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

    /**
     * Publish given path.
     *
     * @param string $path Path
     *
     * @return string Published url (/assets/images/image1.png)
     */
    public function publish($path)
    {
        if (!isset(static::$_cachePublishPath[$path])) {
            static::$_cachePublishPath[$path] = Yii::$app->assetManager->publish($path)[1];
        }
        return static::$_cachePublishPath[$path];
    }
} 
