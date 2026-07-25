<?php

declare(strict_types=1);

namespace app\controllers\users;

use app\models\users\Role;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RolesController extends \app\controllers\Controller
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
            'query' => Role::find()
                ->where(['type' => Role::TYPE_ROLE])
                ->orderBy(['created_at' => SORT_DESC, 'name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(string $id): string
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

    public function actionPermissions(string $id): string
    {
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('permissions', [
                'model' => $this->findModel($id),
            ]);
        }

        return $this->render('permissions', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate(): Response|array|string
    {
        $model = new Role();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            $model->permissionNames = $this->extractPermissionNames($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            $model->permissionNames = $this->extractPermissionNames($request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->saveWithPermissions()) {
                return ['success' => true, 'message' => 'Role saved successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post())) {
            $model->permissionNames = $this->extractPermissionNames($request->post());
            if ($model->saveWithPermissions()) {
                Yii::$app->session->setFlash('success', 'Role saved successfully.');
                return $this->redirect(['index']);
            }
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
            $model->permissionNames = $this->extractPermissionNames($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            $model->permissionNames = $this->extractPermissionNames($request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->saveWithPermissions()) {
                return ['success' => true, 'message' => 'Role updated successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post())) {
            $model->permissionNames = $this->extractPermissionNames($request->post());
            if ($model->saveWithPermissions()) {
                Yii::$app->session->setFlash('success', 'Role updated successfully.');
                return $this->redirect(['index']);
            }
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
                return $this->asJson(['success' => true, 'message' => 'Role deleted successfully.']);
            }

            Yii::$app->session->setFlash('success', 'Role deleted successfully.');
        } catch (IntegrityException) {
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $this->asJson(['success' => false, 'message' => 'Role cannot be deleted because it is in use.']);
            }

            Yii::$app->session->setFlash('error', 'Role cannot be deleted because it is in use.');
        }

        return $this->redirect(['index']);
    }

    protected function findModel(string $name): Role
    {
        if (($model = Role::findOne(['name' => $name, 'type' => Role::TYPE_ROLE])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function extractPermissionNames(array $postData): array
    {
        return array_values(array_filter(array_map('strval', (array) ($postData['Role']['permissionNames'] ?? []))));
    }
}
