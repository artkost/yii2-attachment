<?php

namespace app\modules\attachment\models;

use app\modules\attachment\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ImageFile
 * @package app\modules\attachment\models
 */
class ImageFile extends AttachmentFile
{

    /**
     * @var array
     */
    public $validate = [
        'extensions' => 'jpg, png',
        'mimeTypes' => 'image/jpeg, image/png'
    ];

    public static function type()
    {
        return 'image';
    }

    public static function styles()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ArrayHelper::merge([['file'], 'file'], $this->validate);

        return $rules;
    }

    /**
     * @param $name
     * @return string
     */
    public function styleUrl($name)
    {
        return $this->stylesUrl() . $name . '/' . $this->uri;
    }

    /**
     * @param $name
     * @return string
     */
    public function stylePath($name)
    {
        return $this->stylesPath() . $name . DIRECTORY_SEPARATOR . $this->uri;
    }

    /**
     * @param $name
     * @return bool
     */
    public function styleExists($name)
    {
        return is_file($this->stylePath($name));
    }

    /**
     * @param $name
     * @return bool status
     */
    public function styleSave($name)
    {
        if (isset(static::styles()[$name])) {
            $method = 'saveStyle' . ucfirst(static::styles()[$name]);

            if (method_exists($this, $method)) {
                return $this->$method();
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function stylesPath()
    {
        return Module::getInstance()->getStoragePath() . 'styles' . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function stylesUrl()
    {
        return Module::getInstance()->getStorageUrl() . 'styles/';
    }

    public function deleteStyles()
    {
        foreach (static::styles() as $name) {
            if ($this->styleExists($name)) {
                unlink($this->stylePath($name));
            }
        }
    }

    public function saveStyles()
    {
        foreach (static::styles() as $name) {
            $this->styleSave($name);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $this->deleteStyles();

        return parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->saveStyles();
        }

        parent::afterSave($insert, $changedAttributes);
    }
}
