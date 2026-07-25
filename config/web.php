    <?php

    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();

    $params = require __DIR__ . '/params.php';
    $config = [
        'params' => $params,
        'id' => 'basic',
        'basePath' => dirname(__DIR__),
        'bootstrap' => ['log'],
        'components' => [
            'assetManager' => [
                'appendTimestamp' => true,
            ],
        ],
        'on beforeRequest' => static function () {
            if (PHP_SAPI === 'cli') {
                return;
            }

            $app = \Yii::$app;
            $route = trim((string) $app->request->pathInfo, '/');
            $publicRoutes = [
                'site/login',
                'site/error',
                'site/captcha',
                'payments/stk-callback',
                'payments/c2b-validation',
                'payments/c2b-confirmation',
            ];

            $isInternalRoute = str_starts_with($route, 'debug/')
                || str_starts_with($route, 'gii/')
                || str_starts_with($route, 'assets/')
                || str_starts_with($route, 'web/assets/');

            if ($isInternalRoute) {
                return;
            }

            // if ($app->user->isGuest && !in_array($route, $publicRoutes, true)) {
            //     if ($app->request->isGet && !$app->request->isAjax) {
            //         $app->user->setReturnUrl($app->request->url);
            //     }
            //     $app->response->redirect(['site/login'])->send();
            //     $app->end();
            // }
        },
        'container' => [
            'singletons' => [
                \yii\mail\MailerInterface::class => [
                    'class' => \yii\symfonymailer\Mailer::class,
                    // send all mails to a file by default.
                    'useFileTransport' => true,
                    'viewPath' => '@app/mail',
                ],
            ],
        ],
        'aliases' => [
            '@bower' => '@vendor/bower-asset',
            '@npm'   => '@vendor/npm-asset',
        ],
        'components' => [

            'authManager' => [
                'class' => 'yii\rbac\DbManager',
            ],
            
            'queue' => [
                'class' => \yii\queue\db\Queue::class,
                'db' => 'db',
                'tableName' => '{{%queue}}',
                'channel' => 'default',
                'mutex' => \yii\mutex\MysqlMutex::class,
            ],

          

            'sms' => [
                'class' => app\components\Sms::class,
                'apiKey' => $params['SMS']['API_KEY'],
                'apiUrl' => $params['SMS']['API_URL'],
                'shortCode' => $params['SMS']['SHORT_CODE'],
                'partnerId' => $params['SMS']['PARTNER_ID'],
                'passType' => $params['SMS']['PASS_TYPE'],
            ],
            'request' => [
                // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
                'cookieValidationKey' => 'o4Dttt32jAh-q_Y5O2Sk7s8Xc1KcXRAx',
            ],
            'cache' => [
                'class' => \yii\caching\FileCache::class,
            ],
            'user' => [
                'identityClass' => \app\models\User::class,
                'enableAutoLogin' => false,
                'authTimeout' => 180, // 3 minutes
            ],
            'session' => [
                'timeout' => 180, // 3 minutes
            ],
            'errorHandler' => [
                'errorAction' => 'site/error',
            ],
            'mailer' => \yii\mail\MailerInterface::class,
            'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => \yii\log\FileTarget::class,
                        'levels' => ['error', 'warning'],
                    ],
                    [
                        'class' => \yii\log\FileTarget::class,
                        'levels' => ['info', 'warning', 'error'],
                        'categories' => ['sms'],
                        'logFile' => '@runtime/logs/sms.log',
                        'logVars' => [],
                    ],
                    [
                        'class' => \yii\log\FileTarget::class,
                        'levels' => ['info', 'warning', 'error'],
                        'categories' => ['mpesa'],
                        'logFile' => '@runtime/logs/mpesa.log',
                        'logVars' => [],
                    ],
                ],
            ],
            'db' => require __DIR__ . '/db.php',

            'urlManager' => [
                'enablePrettyUrl' => true,
                'showScriptName' => false,
                'rules' => [
                    'POST payments/stk-callback'     => 'payments/stk-callback',
                    'POST payments/c2b-validation'   => 'payments/c2b-validation',
                    'POST payments/c2b-confirmation' => 'payments/c2b-confirmation',
                    'POST payments/initiate-stk'     => 'payments/initiate-stk',
                    'POST payments/register-urls'    => 'payments/register-urls',
                ],
            ],

        ],
        
    ];

    if (YII_ENV_DEV) {
        // configuration adjustments for 'dev' environment
        $config['bootstrap'][] = 'debug';
        $config['modules']['debug'] = [
            'class' => \yii\debug\Module::class,
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
        ];

        $config['bootstrap'][] = 'gii';
        $config['modules']['gii'] = [
            'class' => \yii\gii\Module::class,
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
        ];
    }

    return $config;
