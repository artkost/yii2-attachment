<?php
return [
    'id' => 'yii2-test--console',
    'basePath' => dirname(__DIR__),
    'components' => [
        'log' => null,
        'cache' => null,
        'attachmentManager' => require __DIR__ . '/manager.php',
        'db' => require __DIR__ . '/db.php',
    ],
];
