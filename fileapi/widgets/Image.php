<?php

namespace app\modules\attachment\fileapi\widgets;

use app\modules\attachment\fileapi\assets\ImageAsset;
use app\modules\attachment\fileapi\CropAsset;
use yii\helpers\Json;

class Image extends File
{
    /**
     * @var boolean Enable/disable files preview
     */
    public $preview = true;

    /**
     * @var boolean Enable/disable crop
     */
    public $crop = false;

    /**
     * @var array JCrop settings
     */
    public $jcropSettings = [
        'aspectRatio' => 1,
        'bgColor' => '#ffffff',
        'maxSize' => [568, 800],
        'minSize' => [100, 100],
        'keySupport' => false, // Important param to hide jCrop radio button.
        'selection' => '100%'
    ];

    /**
     * Register widget asset.
     */
    public function registerClientScript()
    {
        $view = $this->getView();

        ImageAsset::register($view);

        if ($this->crop === true) {
            CropAsset::register($view);
        }

        $selector = $this->getSelector();
        $options = Json::encode($this->settings);

        $view->registerJs("jQuery('#$selector').yiiAttachmentFileAPI('image', $options);");
    }
}
