<?php

declare(strict_types=1);

namespace app\controllers\teachers;

use Yii;
use app\models\ClassTeacher;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ClassTeachersController extends Controller
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
        $selectedIds = $this->getIndexRecordIds();

        $dataProvider = new ActiveDataProvider([
            'query' => ClassTeacher::find()
                ->with(['gradeStream.grade', 'gradeStream.stream', 'teacher', 'academicYear'])
                ->andFilterWhere(['id' => $selectedIds])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHistory(int $gradeStreamId): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ClassTeacher::find()
                ->with(['gradeStream.grade', 'gradeStream.stream', 'teacher', 'academicYear'])
                ->where(['grade_stream_id' => $gradeStreamId])
                ->orderBy(['is_current' => SORT_DESC, 'id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $gradeStreamLabel = ClassTeacher::find()
            ->with(['gradeStream.grade', 'gradeStream.stream'])
            ->where(['grade_stream_id' => $gradeStreamId])
            ->orderBy(['id' => SORT_DESC])
            ->one()?->getGradeStreamLabel() ?? 'Unknown Grade Stream';

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'gradeStreamId' => $gradeStreamId,
            'gradeStreamLabel' => $gradeStreamLabel,
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
        $model = new ClassTeacher();
        $model->is_current = ClassTeacher::CURRENT_YES;
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['success' => true, 'message' => 'Class teacher saved successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Class teacher saved successfully.');
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
                return ['success' => true, 'message' => 'Class teacher updated successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Class teacher updated successfully.');
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
            return ['success' => true, 'message' => 'Class teacher deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Class teacher deleted successfully.');
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): ClassTeacher
    {
        if (($model = ClassTeacher::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function getIndexRecordIds(): array
    {
        $currentIds = ClassTeacher::find()
            ->select('MAX(id)')
            ->where(['is_current' => ClassTeacher::CURRENT_YES])
            ->groupBy('grade_stream_id')
            ->column();

        $gradeStreamsWithCurrent = ClassTeacher::find()
            ->select('grade_stream_id')
            ->where(['is_current' => ClassTeacher::CURRENT_YES])
            ->groupBy('grade_stream_id');

        $latestFallbackIds = ClassTeacher::find()
            ->select('MAX(id)')
            ->andFilterWhere(['not in', 'grade_stream_id', $gradeStreamsWithCurrent])
            ->groupBy('grade_stream_id')
            ->column();

        return array_values(array_unique(array_merge($currentIds, $latestFallbackIds)));
    }
}
