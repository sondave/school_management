<?php

declare(strict_types=1);

namespace app\controllers\users;

use app\models\users\Permission;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PermissionsController extends Controller
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
            'query' => Permission::find()
                ->where(['type' => Permission::TYPE_PERMISSION])
                ->with(['group'])
                ->orderBy(['created_at' => SORT_DESC, 'name' => SORT_ASC]),
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
        $model = new Permission();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['success' => true, 'message' => 'Permission saved successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Permission saved successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(string $id): Response|array|string
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
                return ['success' => true, 'message' => 'Permission updated successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Permission updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(string $id): Response
    {
        $model = $this->findModel($id);
        $request = Yii::$app->request;

        try {
            $model->delete();

            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->asJson(['success' => true, 'message' => 'Permission deleted successfully.']);
            }

            Yii::$app->session->setFlash('success', 'Permission deleted successfully.');
        } catch (IntegrityException) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->asJson(['success' => false, 'message' => 'Permission cannot be deleted because it is in use.']);
            }

            Yii::$app->session->setFlash('error', 'Permission cannot be deleted because it is in use.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel(string $name): Permission
    {
        if (($model = Permission::findOne(['name' => $name, 'type' => Permission::TYPE_PERMISSION])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
