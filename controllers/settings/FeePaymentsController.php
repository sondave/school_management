<?php

declare(strict_types=1);

namespace app\controllers\settings;

use app\models\fees\FeePayment;
use app\models\fees\StudentFeeCharge;
use app\models\settings\AcademicYear;
use app\models\settings\Grade;
use app\models\settings\SchoolInfo;
use app\models\settings\Term;
use app\services\FeePaymentService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeePaymentsController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'create' => ['GET', 'POST'],
                        'charges' => ['GET'],
                        'view-allocations' => ['GET'],
                        'receipt' => ['GET'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FeePayment::find()
                ->with([
                    'student',
                    'createdByUser',
                    'allocations.studentFeeCharge.feeStructure.academicYear',
                    'allocations.studentFeeCharge.feeStructure.term',
                    'allocations.studentFeeCharge.feeStructure.grade',
                ])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(): Response|array|string
    {
        $model = new FeePayment();
        $model->payment_date = date('Y-m-d');

        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $allocations = (array) $request->post('allocations', []);
            if (FeePaymentService::createPayment($model, $allocations)) {
                return [
                    'success' => true,
                    'message' => 'Payment posted successfully.',
                ];
            }

            return [
                'success' => false,
                'message' => $this->firstError($model) ?? 'Unable to post payment.',
                'html' => $this->renderAjax('_form', [
                    'model' => $model,
                    'initialAllocations' => $allocations,
                ]),
            ];
        }

        if (!$request->isAjax && $model->load($request->post())) {
            $allocations = (array) $request->post('allocations', []);
            if (FeePaymentService::createPayment($model, $allocations)) {
                Yii::$app->session->setFlash('success', 'Payment posted successfully.');
            } else {
                Yii::$app->session->setFlash('error', $this->firstError($model) ?? 'Unable to post payment.');
            }

            return $this->redirect(['index']);
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', [
                'model' => $model,
                'initialAllocations' => [],
            ]);
        }

        return $this->redirect(['index']);
    }

    public function actionCharges(int $studentId, int $academicYearId, int $termId, int $gradeId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $charges = StudentFeeCharge::find()
            ->alias('sfc')
            ->joinWith(['feeStructure fs', 'feeStructure.category'])
            ->where([
                'sfc.student_id' => $studentId,
                'fs.academic_year_id' => $academicYearId,
                'fs.term_id' => $termId,
                'fs.grade_id' => $gradeId,
                'fs.status' => 1,
            ])
            ->andWhere(['>', 'sfc.balance', 0])
            ->orderBy(['fs.category_id' => SORT_ASC])
            ->all();

        $items = [];
        foreach ($charges as $charge) {
            $items[] = [
                'id' => (int) $charge->id,
                'label' => (string) ($charge->feeStructure?->category?->name ?? 'Fee Item'),
                'amount' => (float) $charge->amount,
                'discount' => (float) $charge->discount,
                'balance' => (float) $charge->balance,
                'checked' => true,
            ];
        }

        return ['success' => true, 'items' => $items];
    }

    public function actionViewAllocations(int $id): string
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view-allocations', [
                'model' => $model,
            ]);
        }

        return $this->render('view-allocations', [
            'model' => $model,
        ]);
    }

    public function actionReceipt(int $id): string
    {
        $model = $this->findModel($id);
        $schoolInfo = SchoolInfo::find()->orderBy(['id' => SORT_ASC])->one();

        return $this->render('receipt', [
            'model' => $model,
            'schoolInfo' => $schoolInfo,
        ]);
    }

    protected function findModel(int $id): FeePayment
    {
        $model = FeePayment::find()
            ->with(['student', 'createdByUser', 'allocations.studentFeeCharge.feeStructure.category'])
            ->where(['id' => $id])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested payment does not exist.');
    }

    public static function getAcademicYearOptions(): array
    {
        return AcademicYear::find()
            ->select(['year', 'id'])
            ->orderBy(['year' => SORT_DESC])
            ->indexBy('id')
            ->column();
    }

    public static function getTermOptions(): array
    {
        return Term::find()
            ->select(['name', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    private function firstError($model): ?string
    {
        foreach ($model->getFirstErrors() as $error) {
            return (string) $error;
        }

        return null;
    }
}
