<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Student;
use app\models\StudentEnrollment;
use app\models\StudentParent;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentsController extends \app\controllers\Controller
{
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'add-parent' => ['GET', 'POST'],
                        'remove-parent' => ['POST'],
                        'add-enrollment' => ['GET', 'POST'],
                        'set-current-enrollment' => ['POST'],
                        'delete-enrollment' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Student::find()->with(['genderLookup', 'statusLookup', 'currentEnrollment.grade'])->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(): string|Response|array
    {
        $model = new Student();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Student created successfully.',
                    'redirectUrl' => Yii::$app->urlManager->createUrl(['students/profile', 'id' => $model->id, 'tab' => 'profile']),
                ];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Student created successfully.');
            return $this->redirect(['profile', 'id' => $model->id, 'tab' => 'profile']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id): string|Response|array
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Student updated successfully.',
                ];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Student updated successfully.');
            return $this->redirect(['profile', 'id' => $model->id, 'tab' => 'profile']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionProfile(int $id, string $tab = 'profile'): string
    {
        $model = $this->findModel($id);

        $parentModel = new StudentParent();
        $parentModel->student_id = (int) $id;

        $enrollmentModel = new StudentEnrollment();
        $enrollmentModel->student_id = (int) $id;
        $enrollmentModel->is_current = 1;

        return $this->render('profile', [
            'model' => $model,
            'activeTab' => $tab,
            'parentModel' => $parentModel,
            'enrollmentModel' => $enrollmentModel,
        ]);
    }

    public function actionView(int $id): Response
    {
        return $this->redirect(['profile', 'id' => $id, 'tab' => 'profile']);
    }

    public function actionAddParent(int $id): Response|array|string
    {
        $student = $this->findModel($id);
        $request = Yii::$app->request;

        $model = new StudentParent();
        $model->student_id = $id;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Parent linked successfully.',
                ];
            }

            return [
                'success' => false,
                'html' => $this->renderAjax('_parent_form', [
                    'model' => $model,
                    'student' => $student,
                ]),
            ];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_parent_form', [
                'model' => $model,
                'student' => $student,
            ]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Parent linked successfully.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($model) ?: 'Unable to link parent.');
        }

        return $this->redirect(['profile', 'id' => $id, 'tab' => 'parents']);
    }

    public function actionRemoveParent(int $id, int $parentId): Response
    {
        $this->findModel($id);

        StudentParent::deleteAll([
            'student_id' => $id,
            'parent_id' => $parentId,
        ]);

        Yii::$app->session->setFlash('success', 'Parent unlinked successfully.');
        return $this->redirect(['profile', 'id' => $id, 'tab' => 'parents']);
    }

    public function actionAddEnrollment(int $id): Response|array|string
    {
        $student = $this->findModel($id);
        $request = Yii::$app->request;

        $model = new StudentEnrollment();
        $model->student_id = $id;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($model->save()) {
                return [
                    'success' => true,
                    'message' => 'Enrollment saved successfully.',
                ];
            }

            return [
                'success' => false,
                'html' => $this->renderAjax('_enrollment_form', [
                    'model' => $model,
                    'student' => $student,
                ]),
            ];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_enrollment_form', [
                'model' => $model,
                'student' => $student,
            ]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Enrollment saved successfully.');
        } else {
            Yii::$app->session->setFlash('error', $this->firstError($model) ?: 'Unable to save enrollment.');
        }

        return $this->redirect(['profile', 'id' => $id, 'tab' => 'enrollment']);
    }

    public function actionSetCurrentEnrollment(int $id, int $enrollmentId): Response
    {
        $this->findModel($id);

        $enrollment = StudentEnrollment::findOne([
            'id' => $enrollmentId,
            'student_id' => $id,
        ]);

        if ($enrollment === null) {
            throw new NotFoundHttpException('Enrollment record not found.');
        }

        $enrollment->is_current = 1;
        $enrollment->save(false);

        Yii::$app->session->setFlash('success', 'Current enrollment updated successfully.');
        return $this->redirect(['profile', 'id' => $id, 'tab' => 'enrollment']);
    }

    public function actionDeleteEnrollment(int $id, int $enrollmentId): Response
    {
        $this->findModel($id);

        StudentEnrollment::deleteAll([
            'id' => $enrollmentId,
            'student_id' => $id,
        ]);

        Yii::$app->session->setFlash('success', 'Enrollment deleted successfully.');
        return $this->redirect(['profile', 'id' => $id, 'tab' => 'enrollment']);
    }

    public function actionDelete(int $id): Response|array
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Student deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Student deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Student
    {
        if (($model = Student::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function firstError($model): ?string
    {
        foreach ($model->getFirstErrors() as $error) {
            return (string) $error;
        }

        return null;
    }
}
