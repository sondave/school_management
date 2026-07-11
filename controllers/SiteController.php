<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\ContactForm;
use app\models\Customer;
use app\models\Loan;
use app\models\LoginForm;
use app\models\Payment;
use app\models\SetPasswordForm;
use app\models\User;
use app\models\settings\General;
use app\services\SystemNotificationService;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'set-password', 'read-notifications', 'read-notification'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['set-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['read-notifications'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['read-notification'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'read-notifications' => ['post'],
                    'read-notification' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        Yii::$app->session->setFlash('success', 'Welcome to Suswa Loan Management System!');
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        $this->layout = 'auth'; // Switches to the login layout style
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

        if ($model->load($this->request->post()) && $model->login()) {
            if ($model->requiresPasswordReset) {
                return $this->redirect(['site/set-password']);
            }

            return $this->goBack();
        }

        if ($model->hasErrors()) {
            Yii::$app->session->setFlash('error', $model->getFirstError('password') ?: $model->getFirstError('username'));
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionSetPassword(): Response|string
    {
        $this->layout = 'auth';

        $user = Yii::$app->user->identity;
        if (!$user instanceof User) {
            return $this->redirect(['site/login']);
        }

        if ((int) $user->status !== User::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', 'Account is not active for password setup.');
            return $this->redirect(['site/login']);
        }

        if ((int) $user->is_first_login !== 1) {
            return $this->goHome();
        }

        $expiry = $user->activation_pas_expires_at;
        if ($expiry === null || strtotime($expiry) <= time()) {
            Yii::$app->session->setFlash('error', 'Activation password has expired. Please contact admin.');
            Yii::$app->user->logout();
            return $this->redirect(['site/login']);
        }

        $model = new SetPasswordForm($user);
        if ($model->load($this->request->post()) && $model->resetPassword($this->security)) {
            Yii::$app->session->setFlash('success', 'Password set successfully. Please login with your new password.');
            Yii::$app->user->logout();
            return $this->redirect(['site/login']);
        }

        return $this->render('set-password', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact(): Response|string
    {
        $model = new ContactForm();

        $contact = $model->load($this->request->post()) && $model->contact(
            $this->mailer,
            Yii::$app->params['adminEmail'],
            Yii::$app->params['senderEmail'],
            Yii::$app->params['senderName'],
        );

        if ($contact) {
            Yii::$app->session->setFlash(
                'success',
                'Thank you for contacting us. We will respond to you as soon as possible.',
            );

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout(): string
    {
        return $this->render('about');
    }

    public function actionReadNotifications(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 401;
            return [
                'success' => false,
                'message' => 'Authentication required.',
            ];
        }

        SystemNotificationService::markAllAsReadForUser((int) Yii::$app->user->id);

        return [
            'success' => true,
            'unreadCount' => 0,
        ];
    }

    public function actionReadNotification(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            Yii::$app->response->statusCode = 401;
            return [
                'success' => false,
                'message' => 'Authentication required.',
            ];
        }

        $type = trim((string) Yii::$app->request->post('type', ''));
        if (!SystemNotificationService::isSupportedType($type)) {
            Yii::$app->response->statusCode = 422;
            return [
                'success' => false,
                'message' => 'Invalid notification type.',
            ];
        }

        SystemNotificationService::markNotificationTypeAsReadForUser((int) Yii::$app->user->id, $type);

        return [
            'success' => true,
        ];
    }
}
