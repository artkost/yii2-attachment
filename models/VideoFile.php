<?php

namespace artkost\attachment\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class VideoFile
 * @package artkost\attachment\models
 */
class MediaFile extends AttachmentFile
{
    /**
     * @var array
     */
    public static $extensions = ['webm', 'ogg', 'mp4'];

    /**
     * @var array
     */
    public static $mimeTypes = ['video/webm', 'video/ogg', 'video/mp4'];

    public static function type()
    {
        return 'video';
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
