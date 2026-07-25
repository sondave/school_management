<?php

declare(strict_types=1);

namespace app\controllers\teachers;

use Yii;
use app\models\TeacherSubject;
use yii\data\ArrayDataProvider;
use yii\db\Transaction;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TeacherSubjectsController extends Controller
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
        $dataProvider = new ArrayDataProvider([
            'allModels' => $this->buildIndexRows(),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'attributes' => ['teacher_label', 'grade_label', 'academic_year_label', 'start_date', 'end_date'],
                'defaultOrder' => ['start_date' => SORT_DESC],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id): string
    {
        $model = $this->loadGroupedModel($id);

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
        $model = new TeacherSubject();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($this->saveAssignmentGroup($model)) {
                return ['success' => true, 'message' => 'Teacher subjects saved successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $this->saveAssignmentGroup($model)) {
            Yii::$app->session->setFlash('success', 'Teacher subjects saved successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate(int $id): Response|array|string
    {
        $model = $this->loadGroupedModel($id);
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($this->saveAssignmentGroup($model)) {
                return ['success' => true, 'message' => 'Teacher subjects updated successfully.'];
            }

            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $this->saveAssignmentGroup($model)) {
            Yii::$app->session->setFlash('success', 'Teacher subjects updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response|array
    {
        $model = $this->findModel($id);
        $groupRows = $this->findGroupRows($model);
        $groupIds = array_map(static fn(TeacherSubject $row): int => (int) $row->id, $groupRows);

        if ($groupIds !== []) {
            TeacherSubject::deleteAll(['id' => $groupIds]);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Teacher subjects deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Teacher subjects deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionSubjectsByGrade(int $gradeId): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $options = [];
        foreach (TeacherSubject::getSubjectOptionsByGrade($gradeId) as $id => $label) {
            $options[] = [
                'id' => (int) $id,
                'label' => $label,
            ];
        }

        return [
            'success' => true,
            'options' => $options,
        ];
    }

    protected function findModel(int $id): TeacherSubject
    {
        if (($model = TeacherSubject::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function loadGroupedModel(int $id): TeacherSubject
    {
        $anchor = $this->findModel($id);
        $groupRows = $this->findGroupRows($anchor);
        $subjectIds = array_map(static fn(TeacherSubject $row): int => (int) $row->subject_id, $groupRows);

        $anchor->subject_ids = $subjectIds;
        $anchor->groupRowIds = array_map(static fn(TeacherSubject $row): int => (int) $row->id, $groupRows);

        return $anchor;
    }

    /**
     * @return TeacherSubject[]
     */
    private function findGroupRows(TeacherSubject $model): array
    {
        return TeacherSubject::find()
            ->with(['teacher', 'grade', 'academicYear', 'subject'])
            ->where([
                'teacher_id' => (int) $model->teacher_id,
                'grade_id' => (int) $model->grade_id,
                'academic_year_id' => (int) $model->academic_year_id,
                'start_date' => $model->start_date,
            ])
            ->orderBy(['subject_id' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    private function saveAssignmentGroup(TeacherSubject $model): bool
    {
        $model->subject_ids = array_values(array_unique(array_map('intval', $model->subject_ids)));
        $model->subject_ids = array_values(array_filter($model->subject_ids, static fn(int $id): bool => $id > 0));

        if (!$model->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction(Transaction::SERIALIZABLE);

        try {
            if ($model->groupRowIds !== []) {
                TeacherSubject::deleteAll(['id' => $model->groupRowIds]);
            }

            foreach ($model->subject_ids as $subjectId) {
                $row = new TeacherSubject();
                $row->teacher_id = (int) $model->teacher_id;
                $row->grade_id = (int) $model->grade_id;
                $row->academic_year_id = (int) $model->academic_year_id;
                $row->subject_id = (int) $subjectId;
                $row->start_date = $model->start_date;
                $row->end_date = $model->end_date;

                if (!$row->save(false)) {
                    throw new \RuntimeException('Unable to save teacher subject assignment.');
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error($throwable->getMessage(), __METHOD__);
            $model->addError('subject_ids', 'Unable to save teacher subjects at the moment.');
            return false;
        }
    }

    private function buildIndexRows(): array
    {
        $models = TeacherSubject::find()
            ->with(['teacher', 'grade', 'academicYear', 'subject'])
            ->orderBy(['start_date' => SORT_DESC, 'id' => SORT_DESC])
            ->all();

        $rows = [];
        foreach ($models as $model) {
            $groupKey = implode(':', [
                (int) $model->teacher_id,
                (int) $model->grade_id,
                (int) $model->academic_year_id,
                (string) $model->start_date,
            ]);

            if (!isset($rows[$groupKey])) {
                $rows[$groupKey] = [
                    'id' => (int) $model->id,
                    'teacher_label' => $model->getTeacherLabel(),
                    'grade_label' => $model->getGradeLabel(),
                    'academic_year_label' => $model->getAcademicYearLabel(),
                    'subjects' => [],
                    'subjects_label' => '',
                    'start_date' => $model->start_date,
                    'end_date' => $model->end_date,
                ];
            }

            $rows[$groupKey]['subjects'][] = $model->getSubjectLabel();
        }

        foreach ($rows as &$row) {
            $subjects = array_values(array_unique($row['subjects']));
            sort($subjects);
            $row['subjects_label'] = implode(', ', $subjects);
        }
        unset($row);

        return array_values($rows);
    }
}