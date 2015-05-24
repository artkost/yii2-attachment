<?php
$config = [
    'id' => 'yii2-attachment-test',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'artkost\attachment\Bootstrap',
    ],
    'extensions' => require(VENDOR_DIR . '/yiisoft/extensions.php'),
    'aliases' => [
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower',
        '@tests/codeception/config' => '@tests/codeception/_config',
    ],
    'components' => [
        'assetManager' => [
            'basePath' => __DIR__ . '/../assets',
        ],
        'log' => null,
        'cache' => null,
        'request' => [
            'enableCsrfValidation' => false,
            'enableCookieValidation' => false,
        ],
        'attachmentManager' => require __DIR__ . '/manager.php',
        'db' => require __DIR__ . '/db.php',
    ]];

if (defined('YII_APP_BASE_PATH')) {
    $config = Codeception\Configuration::mergeConfigs(
        $config,
        require YII_APP_BASE_PATH . '/tests/codeception/config/config.php'
    );
}
return $config;
