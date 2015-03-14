<?php

namespace artkost\attachment\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ImageFile
 * @package artkost\attachment\models
 */
class ImageFile extends AttachmentFile
{
    /**
     * @var array
     */
    public static $extensions = ['jpg', 'png'];

    /**
     * @var array
     */
    public static $mimeTypes = ['image/jpeg', 'image/png'];

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        $rules[] = ArrayHelper::merge([['file'], 'file'], [
            'extensions' => implode(',', static::$extensions),
            'mimeTypes' => implode(',', static::$mimeTypes)
        ]);

        return $rules;
    }
}
