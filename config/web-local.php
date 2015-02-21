<?php
return [
    'bootstrap' => [
        'debug',
        'gii',
    ],
    'components' => [
        'assetManager' => [
            'linkAssets' => true,
        ],
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1', '::1']
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'generators' => [
                'model' => [
                    'class' => '\pavlinter\adm\gii\generators\model\Generator',
                ],
                'crud' => [
                    'class' => '\pavlinter\adm\gii\generators\crud\Generator',
                ],
                'module' => [
                    'class' => '\pavlinter\adm\gii\generators\module\Generator',
                ],
            ]
        ],
    ],
];

