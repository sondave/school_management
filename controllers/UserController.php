<?php

declare(strict_types=1);

namespace app\controllers;

use app\jobs\SendSmsJob;
use app\models\notifications\SmsNotification;
use app\models\settings\SmsTemplate;
use app\models\User;
use app\models\UserProfile;
use app\models\UserSearch;
use Yii;
use yii\base\Exception;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

class UserController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'activate' => ['POST'],
                        'resend-activation-password' => ['POST'],
                        'block' => ['POST'],
                        'ban' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $user = $this->findModel($id);
        $profile = $user->getProfile()->one();

        return $this->render('view', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function actionCreate(): Response|array|string
    {
        $user = new User();
        $profile = new UserProfile();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $user->load($request->post());
            $profile->load($request->post());
            return ActiveForm::validateMultiple([$user, $profile]);
        }

        if ($user->load($request->post()) && $profile->load($request->post())) {
            $this->normalizeUserInput($user, $profile);

            if ($this->createUser($user, $profile)) {
                Yii::$app->session->setFlash('success', 'User created successfully with default inactive status.');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', $this->firstError($user) ?: $this->firstError($profile) ?: 'Unable to create user.');
        }

        return $this->render('create', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function actionActivate(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = $this->findModel($id);
        $user->status = User::STATUS_ACTIVE;
        $user->activated_at = date('Y-m-d H:i:s');
        $user->blocked_at = null;
        $user->remarks = null;

        if ($user->save(false, ['status', 'activated_at', 'blocked_at', 'remarks'])) {
            return ['success' => true, 'message' => 'User activated successfully.'];
        }

        Yii::$app->response->statusCode = 422;
        return ['success' => false, 'message' => 'Failed to activate user.'];
    }

    public function actionResendActivationPassword(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = $this->findModel($id);
        $activationPassword = $this->generateActivationPassword();

        $user->status = User::STATUS_INACTIVE;
        $user->is_first_login = 1;
        $user->activation_pas_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $user->login_attempts = 0;
        $user->blocked_at = null;
        $user->setPasswordHash(Yii::$app->security->generatePasswordHash($activationPassword));

        if (!$user->save(false, ['status', 'is_first_login', 'activation_pas_expires_at', 'login_attempts', 'blocked_at', 'password_hash'])) {
            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'Unable to regenerate activation password.'];
        }

        $profile = UserProfile::findOne(['user_id' => (int) $user->id]);
        if ($profile !== null) {
            $this->queueUserActivationAlert($user, $profile, $activationPassword, 'USERS_USER_ACTIVATION_RESEND_ALERT');
        }

        return [
            'success' => true,
            'message' => 'Activation password regenerated successfully and sent to the user.',
        ];
    }

    public function actionBlock(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $remarks = trim((string) Yii::$app->request->post('remarks', ''));
        if ($remarks === '') {
            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'Remarks are required.'];
        }

        $user = $this->findModel($id);
        $user->status = User::STATUS_BLOCKED;
        $user->blocked_at = date('Y-m-d H:i:s');
        $user->remarks = $remarks;

        if ($user->save(false, ['status', 'blocked_at', 'remarks'])) {
            return ['success' => true, 'message' => 'User blocked successfully.'];
        }

        Yii::$app->response->statusCode = 422;
        return ['success' => false, 'message' => 'Failed to block user.'];
    }

    public function actionBan(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $remarks = trim((string) Yii::$app->request->post('remarks', ''));
        if ($remarks === '') {
            Yii::$app->response->statusCode = 422;
            return ['success' => false, 'message' => 'Remarks are required.'];
        }

        $user = $this->findModel($id);
        $user->status = User::STATUS_BANNED;
        $user->blocked_at = date('Y-m-d H:i:s');
        $user->remarks = $remarks;

        if ($user->save(false, ['status', 'blocked_at', 'remarks'])) {
            return ['success' => true, 'message' => 'User banned successfully.'];
        }

        Yii::$app->response->statusCode = 422;
        return ['success' => false, 'message' => 'Failed to ban user.'];
    }

    private function normalizeUserInput(User $user, UserProfile $profile): void
    {
        $user->username = $this->normalizeLowercase((string) $user->username);
        $user->email = $this->normalizeLowercase((string) $user->email);

        $profile->first_name = $this->normalizeName((string) $profile->first_name);
        $profile->other_names = $this->normalizeName((string) $profile->other_names);
    }

    private function normalizeLowercase(string $value): string
    {
        return mb_strtolower(trim($value), 'UTF-8');
    }

    private function normalizeName(string $value): string
    {
        $trimmedValue = trim($value);
        if ($trimmedValue === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $trimmedValue) ?: [$trimmedValue];
        $normalizedParts = array_map(static function (string $part): string {
            return mb_convert_case($part, MB_CASE_TITLE, 'UTF-8');
        }, $parts);

        return implode(' ', $normalizedParts);
    }

    private function createUser(User $user, UserProfile $profile): bool
    {
        $db = Yii::$app->db;
        /** @var Transaction $transaction */
        $transaction = $db->beginTransaction();

        try {
            $activationPassword = $this->generateActivationPassword();

            $user->status = User::STATUS_INACTIVE;
            $user->is_first_login = 1;
            $user->login_attempts = 0;
            $user->activation_pas_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $user->auth_key = Yii::$app->security->generateRandomString(32);
            $user->access_token = Yii::$app->security->generateRandomString(40);
            $user->setPasswordHash(Yii::$app->security->generatePasswordHash($activationPassword));

            if (!$user->save()) {
                $transaction->rollBack();
                return false;
            }

            $profile->user_id = (int) $user->id;
            if (!$profile->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            $this->queueUserActivationAlert($user, $profile, $activationPassword);
            return true;
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }
            Yii::error($exception->getMessage(), __METHOD__);
            return false;
        }
    }

    private function queueUserActivationAlert(User $user, UserProfile $profile, string $activationPassword, string $templateName = 'USERS_USER_ACTIVATION_ALERT'): void
    {
        $template = SmsTemplate::find()
            ->where(['name' => $templateName, 'status' => 1])
            ->one();

        if ($template === null) {
            return;
        }

        $phoneNumber = trim((string) $profile->phone);
        if ($phoneNumber === '') {
            return;
        }

        $fullName = trim((string) $profile->first_name . ' ' . (string) $profile->other_names);
        $message = $this->replaceTemplateParams((string) $template->template, [
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $profile->first_name,
            'other_names' => $profile->other_names,
            'full_name' => $fullName,
            'name' => $fullName,
            'phone' => $profile->phone,
            'phone_number' => $profile->phone,
            'activation_password' => $activationPassword,
        ]);

        if ($message === '') {
            return;
        }

        $sms = new SmsNotification();
        $sms->tracking_id = 'USER-' . date('YmdHis') . '-' . strtoupper(substr(sha1((string) microtime(true) . (string) random_int(1, PHP_INT_MAX)), 0, 8));
        $sms->sms_template_id = (int) $template->id;
        $sms->recipient_type = 'specific_parent';
        $sms->phone_number = $phoneNumber;
        $sms->message = $message;
        $sms->status = SmsNotification::STATUS_QUEUED;

        if (!$sms->save()) {
            Yii::error([
                'event' => 'user.activation.sms.queue.failed',
                'errors' => $sms->getErrors(),
            ], 'sms');
            return;
        }

        if (Yii::$app->has('queue')) {
            Yii::$app->queue->push(new SendSmsJob([
                'smsId' => $sms->id,
                'passType' => 'base64 encode',
            ]));
        }
    }

    protected function replaceTemplateParams(string $message, array $context): string
    {
        return (string) preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', static function (array $matches) use ($context): string {
            $key = strtolower((string) $matches[1]);
            if (!array_key_exists($key, $context)) {
                return (string) $matches[0];
            }

            $value = $context[$key];
            if ($value === null) {
                return (string) $matches[0];
            }

            return (string) $value;
        }, $message);
    }

    private function findModel(int $id): User
    {
        $model = User::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('The requested user does not exist.');
        }

        return $model;
    }

    private function firstError(User|UserProfile $model): ?string
    {
        $errors = $model->getFirstErrors();
        if ($errors === []) {
            return null;
        }

        return reset($errors) ?: null;
    }

    private function generateActivationPassword(): string
    {
        try {
            $words = ['alpha', 'bravo', 'charlie', 'delta', 'echo', 'foxtrot', 'golf', 'hotel', 'india', 'juliet'];
            $numbers = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];
            $word = $words[array_rand($words)];
            $number = $numbers[array_rand($numbers)];

            return $word . $number;
        } catch (Exception) {
            return substr(strtolower(uniqid('pass', true)), 0, 10);
        }
    }
}
