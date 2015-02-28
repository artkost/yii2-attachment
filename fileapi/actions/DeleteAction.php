<?php

namespace app\modules\attachment\fileapi\actions;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use Yii;

/**
 * DeleteAction for images and files.
 */
class DeleteAction extends Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {

    }

    /**
     * @inheritdoc
     */
    public function run()
    {

    }

    /**
     * @return \yii\web\Request
     */
    protected function getRequest()
    {
        return Yii::$app->request;
    }
}
