<?php

namespace app\modules\attachment\fileapi\assets;

use yii\web\AssetBundle;

/**
 * Widget asset bundle.
 */
class FileAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/modules/attachment/fileapi/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/attachment-widget.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/attachment.FileAPI.js'
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'app\modules\attachment\fileapi\Asset',
    ];
}
