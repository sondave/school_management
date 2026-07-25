<?php

declare(strict_types=1);

namespace app\controllers\notifications;

use app\controllers\Controller;
use app\jobs\SendSmsJob;
use app\models\Parents;
use app\models\Student;
use app\models\notifications\SmsNotification;
use app\models\notifications\SmsNotificationDispatchForm;
use app\models\settings\Grade;
use app\models\settings\SmsTemplate;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use app\controllers\Controller;
use yii\web\Response;

class SmsNotificationController extends Controller
{
    public function actionIndex(): string|Response|array
    {
        $model = new SmsNotificationDispatchForm();

        if (Yii::$app->request->isAjax && Yii::$app->request->post('ajax') === 'sms-notification-form') {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $recipients = $model->resolveRecipients();

            if (empty($recipients)) {
                Yii::$app->session->setFlash('error', 'No valid recipients found for the selected target.');
            } else {
                $this->queueMessages($model, $recipients);
                return $this->redirect(['index']);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => SmsNotification::find()->with(['template', 'parent', 'grade'])->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $templates = SmsTemplate::find()
            ->select(['id', 'name', 'template'])
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'templates' => $templates,
        ]);
    }

    /**
     * @param array<int,array{parent_id:int,student_id:int|null,grade_id:int|null,phone_number:string}> $recipients
     */
    private function queueMessages(SmsNotificationDispatchForm $model, array $recipients): void
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $queuedCount = 0;
        $trackingId = 'BATCH-' . date('YmdHis') . '-' . strtoupper(substr(sha1((string) microtime(true) . (string) random_int(1, PHP_INT_MAX)), 0, 8));

        $parentIds = array_values(array_unique(array_map(static fn(array $item): int => (int) $item['parent_id'], $recipients)));
        $studentIds = array_values(array_unique(array_filter(array_map(static fn(array $item): ?int => $item['student_id'], $recipients), static fn($id): bool => $id !== null)));
        $gradeIds = array_values(array_unique(array_filter(array_map(static fn(array $item): ?int => $item['grade_id'], $recipients), static fn($id): bool => $id !== null)));

        $parents = Parents::find()
            ->select(['id', 'first_name', 'other_names', 'phone_no'])
            ->where(['id' => $parentIds])
            ->indexBy('id')
            ->asArray()
            ->all();

        $students = empty($studentIds)
            ? []
            : Student::find()
                ->select(['id', 'first_name', 'middle_name', 'surname'])
                ->where(['id' => $studentIds])
                ->indexBy('id')
                ->asArray()
                ->all();

        $grades = empty($gradeIds)
            ? []
            : Grade::find()
                ->select(['id', 'grade'])
                ->where(['id' => $gradeIds])
                ->indexBy('id')
                ->asArray()
                ->all();

        try {
            foreach ($recipients as $recipient) {
                $parent = $parents[(int) $recipient['parent_id']] ?? null;
                $student = isset($recipient['student_id']) ? ($students[(int) $recipient['student_id']] ?? null) : null;
                $grade = isset($recipient['grade_id']) ? ($grades[(int) $recipient['grade_id']] ?? null) : null;

                $renderedMessage = $this->replaceTemplateParams((string) $model->message, [
                    'first_name' => $parent['first_name'] ?? null,
                    'other_names' => $parent['other_names'] ?? null,
                    'full_name' => isset($parent['first_name'], $parent['other_names'])
                        ? trim((string) $parent['first_name'] . ' ' . (string) $parent['other_names'])
                        : null,
                    'name' => isset($parent['first_name'], $parent['other_names'])
                        ? trim((string) $parent['first_name'] . ' ' . (string) $parent['other_names'])
                        : null,
                    'phone_no' => $parent['phone_no'] ?? null,
                    'phone_number' => $parent['phone_no'] ?? null,
                    'student_first_name' => $student['first_name'] ?? null,
                    'student_middle_name' => $student['middle_name'] ?? null,
                    'student_surname' => $student['surname'] ?? null,
                    'student_full_name' => isset($student['first_name'], $student['middle_name'], $student['surname'])
                        ? trim((string) $student['first_name'] . ' ' . (string) $student['middle_name'] . ' ' . (string) $student['surname'])
                        : null,
                    'grade' => $grade['grade'] ?? null,
                ]);

                $sms = new SmsNotification();
                $sms->tracking_id = $trackingId;
                $sms->sms_template_id = (int) $model->sms_template_id;
                $sms->recipient_type = (string) $model->recipient_type;
                $sms->parent_id = $recipient['parent_id'];
                $sms->student_id = $recipient['student_id'];
                $sms->grade_id = $recipient['grade_id'];
                $sms->phone_number = (string) $recipient['phone_number'];
                $sms->message = $renderedMessage;
                $sms->status = SmsNotification::STATUS_QUEUED;

                if (!$sms->save()) {
                    throw new Exception('Unable to queue SMS notification.');
                }

                Yii::$app->queue->push(new SendSmsJob([
                    'smsId' => $sms->id,
                ]));

                ++$queuedCount;
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', $queuedCount . ' SMS notification(s) queued successfully.');
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error([
                'event' => 'sms.notification.queue.failed',
                'error' => $e->getMessage(),
            ], 'sms');
            Yii::$app->session->setFlash('error', 'Failed to queue SMS notifications.');
        }
    }

    /**
     * @param array<string,mixed> $context
     */
    private function replaceTemplateParams(string $message, array $context): string
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
}
