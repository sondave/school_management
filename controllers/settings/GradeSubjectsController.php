<?php

declare(strict_types=1);

namespace app\controllers\settings;

use Yii;
use app\models\settings\GradeSubject;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class GradeSubjectsController extends Controller
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
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => GradeSubject::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(): Response|array|string
    {
        $model = new GradeSubject();
        $model->status = GradeSubject::STATUS_ACTIVE;
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['success' => true, 'message' => 'Grade subject saved successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Grade subject saved successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|array|string
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
                return ['success' => true, 'message' => 'Grade subject updated successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Grade subject updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response|array
    {
        $model = $this->findModel($id);
        $model->delete();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Grade subject deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Grade subject deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): GradeSubject
    {
        if (($model = GradeSubject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
