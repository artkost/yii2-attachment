<?php

namespace artkost\attachment;

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
            'basePath' => __DIR__,
            'forceTranslation' => true,
            'fileMap' => [
                'attachment/model' => 'model.php',
            ]
        ];
    }
} 
