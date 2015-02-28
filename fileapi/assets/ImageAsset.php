<?php

namespace app\modules\attachment\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Widget asset bundle.
 */
class ImageAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $depends = [
        'app\modules\attachment\fileapi\assets\FileAsset',
    ];
}
