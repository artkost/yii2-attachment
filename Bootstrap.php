<?php

namespace app\modules\attachment;

use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->i18n->translations['attachment/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/attachment/messages',
            'forceTranslation' => true,
            'fileMap' => [
                'attachment/model' => 'model.php',
                'attachment/widget' => 'widget.php',
            ]
        ];
    }
} 
