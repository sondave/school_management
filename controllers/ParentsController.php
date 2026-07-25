<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use app\models\Parents;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ParentsController extends Controller
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
            'query' => Parents::find()->orderBy(['id' => SORT_DESC]),
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
        $model = Parents::find()
            ->with(['studentParents.student'])
            ->where(['id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(): Response|array|string
    {
        $model = new Parents();
        $model->status = Parents::STATUS_ACTIVE;
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['success' => true, 'message' => 'Parent saved successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Parent saved successfully.');
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
                return ['success' => true, 'message' => 'Parent updated successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Parent updated successfully.');
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
            return ['success' => true, 'message' => 'Parent deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Parent deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Parents
    {
        if (($model = Parents::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
