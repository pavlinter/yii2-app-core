<?php

use pavlinter\display\DisplayImage;

if (YII_ENV_DEV) {
    $params = \yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/params.php'),
        require(__DIR__ . '/params-local.php')
    );
    $db = \yii\helpers\ArrayHelper::merge(
        require(__DIR__ . '/db.php'),
        require(__DIR__ . '/db-local.php')
    );
} else {
    $params = require(__DIR__ . '/params.php');
    $db = require(__DIR__ . '/db.php');
}

Yii::$container->set('pavlinter\display\DisplayImage', [
    'config' => [
        'pages' => [
            'imagesWebDir' => '@web/files/pages',
            'imagesDir' => '@webroot/files/pages',
            'defaultWebDir' => '@web/files/default',
            'defaultDir' => '@webroot/files/default',
            'mode' => DisplayImage::MODE_OUTBOUND,
        ],
    ]
]);


$config = [
    'name' => 'My Application',
    'id' => 'app-core',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'debug',
        'urlManager',
        'i18n',
    ],
    'on beforeRequest' => function ($event) {
        $params = \pavlinter\admparams\models\Params::bootstrap();
        Yii::$app->params = \yii\helpers\ArrayHelper::merge(Yii::$app->params, $params);
        \pavlinter\admeconfig\models\EmailConfig::changeMailConfig();
    },
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['127.0.0.1', '::1']
        ],
        'adm' => [
            'class' => 'pavlinter\adm\Adm',
            'modules' => [
                'appadm',
                'admpages',
                'admgoogletools',
                'admparams',
                'admeconfig',
            ],
            'components' => [
                'manager' => [
                    'userClass' => 'app\models\User',
                    'userSearchClass' => 'app\core\adm\models\UserSearch',
                    /*
                    'loginFormClass' => 'pavlinter\adm\models\LoginForm',
                    'authItemClass' => 'pavlinter\adm\models\AuthItem',
                    'authItemSearchClass' => 'pavlinter\adm\models\AuthItemSearch',
                    'authRuleClass' => 'pavlinter\adm\models\AuthRule',
                    'authRuleSearchClass' => 'pavlinter\adm\models\AuthRuleSearch',
                    'authItemChildClass' => 'pavlinter\adm\models\AuthItemChild',
                    'authItemChildSearchClass' => 'pavlinter\adm\models\AuthItemChildSearch',
                    'authAssignmentClass' => 'pavlinter\adm\models\AuthAssignment',
                    'authAssignmentSearchClass' => 'pavlinter\adm\models\AuthAssignmentSearch',
                    'languageClass' => 'pavlinter\adm\models\Language',
                    'languageSearchClass' => 'pavlinter\adm\models\LanguageSearch',
                    'sourceMessageClass' => 'pavlinter\adm\models\SourceMessage',
                    'sourceMessageSearchClass' => 'pavlinter\adm\models\SourceMessageSearch',
                    'messageClass' => 'pavlinter\adm\models\Message',
                    */
                ],
            ],
            'controllerMap' => [
                'user' => [
                    'class' => 'app\core\adm\controllers\UserController',
                ],
                'elfinder' => function ($config) {
                    $startPath = Yii::$app->request->get('startPath');
                    if ($startPath) {
                        $startPath = Yii::getAlias('@webroot') . '/files' . DIRECTORY_SEPARATOR . strtr($startPath, '::', '/');
                    }
                    if(Yii::$app->user->can('AdmAdmin')) {
                        $config['roots'][] = [
                            'baseUrl'=>'@web',
                            'basePath'=>'@webroot',
                            'path' => 'files',
                            'name' => 'Global',
                            'options' => [
                                'startPath' => $startPath,
                                'uploadMaxSize' => '20M',
                                'attributes' => [
                                    [
                                        'pattern' => '#adm_users$#i',
                                        'read' => false,
                                        'write' => false,
                                        'hidden' => true,
                                        'locked' => false
                                    ]
                                ],
                            ],
                        ];
                    }
                    return $config;
                },
            ],
        ],
        'appadm' => [
            'class' => 'app\modules\appadm\Module',
        ],
        'admgoogletools' => [
            'class' => 'app\modules\admgoogletools\Module',
        ],
        'gridview'=> [
            'class'=>'\kartik\grid\Module',
        ],
        'admpages' => [
            'class' => 'pavlinter\admpages\Module',
            'pageLayouts' => function ($m) {
                return [
                    'main' => $m::t('layouts', 'Main Page', ['dot' => false]),
                    'contact' => $m::t('layouts', 'Contact', ['dot' => false]),
                ];
            },
            'pageRedirect' => [
                'contact' => ['pages/contact'],
            ],
            'pageTypes' => function ($m) {
                return [];
            },
            'pageLayout' => '/main',
            'closeDeletePage' => [1,2,3], //id [2,130]
            'files' => [
                'page' => [
                    'dirs' => [
                        '@webroot/files/pages/{id}/gallery'// {id} - id page
                    ],
                    'startPath' => 'pages::{id}', // where :: replace to /
                ],
                'main' => [
                    'dirs' => [
                        '@webroot/files/pages/{id}/gallery'
                    ],
                    'startPath' => 'pages::{id}',
                ],
            ],
            'components' => [
                'manager' => [
                    'pageSearchClass' => 'app\core\admpages\models\PageSearch',
                ],
            ],
        ],
        'admparams' => [
            'class' => 'pavlinter\admparams\Module',
        ],
        'admeconfig' => [
            'class' => 'pavlinter\admeconfig\Module',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'pavlinter\adm\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'urlManager' => [
            'class'=>'\pavlinter\urlmanager\UrlManager', //https://github.com/pavlinter/yii2-url-manager
            'enableLang' => true,
            'langBegin' => ['en','ru', 'lv'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'ruleConfig' => [
                'class' => '\pavlinter\urlmanager\UrlRule',
            ],
            'rules' => [
                '' => 'admpages/default/main',
                'page/<alias:([A-Za-z0-9_-])+>' => 'admpages/default/index',
            ]
        ],
        'i18n' => [
            'class'=>'pavlinter\translation\I18N', //https://github.com/pavlinter/yii2-dot-translation
            'access' => function () {
                return Yii::$app->getUser()->can('Adm-Transl');
            },
            'dialog' => 'bs',
            'router' => '/adm/source-message/dot-translation',
            'categoryUrl' => '@web/{lang}/adm/source-message/index?SourceMessageSearch[category]={category}',
            'translations' => [
                'app*' => [
                    'class' => 'pavlinter\translation\DbMessageSource',
                    'forceTranslation' => true,
                    'autoInsert' => true,
                    'dotMode' => true,
                ],
                'model*' => [
                    'class' => 'pavlinter\translation\DbMessageSource',
                    'forceTranslation' => true,
                    'autoInsert' => true,
                    'dotMode' => false,
                ],
                'adm*' => [
                    'class' => 'pavlinter\translation\DbMessageSource',
                    'forceTranslation' => true,
                    'autoInsert' => true,
                    'dotMode' => true,
                ],
                'modelAdm*' => [
                    'class' => 'pavlinter\translation\DbMessageSource',
                    'forceTranslation' => true,
                    'autoInsert' => true,
                    'dotMode' => false,
                ],
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'basePath' => '@webroot/assets_b/common',
                    'baseUrl' => '@web/assets_b/common',
                    'sourcePath' => null,   // do not publish the bundle
                ],
            ],
        ],
        'view' => [
            'title' => 'My title',
            'theme' => [
                'pathMap' => [
                    '@vendor/pavlinter/yii2-adm-pages/admpages/views' => '@app/views/admpages',
                    '@vendor/pavlinter/yii2-adm/adm/views' => '@app/core/adm/views',
                    '@vendor/mihaildev/yii2-elfinder/views' => '@app/core/elfinder/views',
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'FONQ3Oh7wxcvVI1ioWvnZ6gJzwjyBV6xU',
        ],
        'formatter' => [
            'datetimeFormat' => 'dd.MM.yyyy HH:mm:ss', // change Yii::$app->params['formatter.mysql.datetimeFormat']
            'dateFormat' => 'dd.MM.yyyy', // change Yii::$app->params['formatter.mysql.dateFormat']
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'EUR',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
];

return $config;
