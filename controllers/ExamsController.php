<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\settings\Exam;
use app\models\settings\ExamGrade;
use app\models\settings\Grade;
use app\models\settings\GradeSubject;
use app\models\settings\AcademicYear;
use app\models\settings\Term;
use app\models\settings\Subject;
use app\models\StudentEnrollment;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\controllers\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ExamsController extends Controller
{
    public function getViewPath(): string
    {
        return Yii::getAlias('@app/views/settings/exams');
    }

    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'grades' => ['GET', 'POST'],
                        'grade-subjects' => ['GET', 'POST'],
                        'submit-marks' => ['GET', 'POST'],
                        'term-options' => ['GET'],
                        'exam-options' => ['GET'],
                        'student-marks' => ['GET', 'POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Exam::find()->with(['academicYear', 'term', 'examType'])->orderBy(['id' => SORT_DESC]),
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
        $model = new Exam();
        $request = Yii::$app->request;

        if ($request->isAjax && $request->post('ajax')) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->load($request->post());
            return \yii\bootstrap5\ActiveForm::validate($model);
        }

        if ($request->isAjax && $model->load($request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return ['success' => true, 'message' => 'Exam saved successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Exam saved successfully.');
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
                return ['success' => true, 'message' => 'Exam updated successfully.'];
            }
            return ['success' => false, 'html' => $this->renderAjax('_form', ['model' => $model])];
        }

        if ($request->isAjax) {
            return $this->renderAjax('_form', ['model' => $model]);
        }

        if ($model->load($request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Exam updated successfully.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete(int $id): Response|array
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand()
                ->delete('exam_marks', ['exam_id' => (int) $model->id])
                ->execute();

            Yii::$app->db->createCommand()
                ->delete('exam_subjects', ['exam_grade_id' => ExamGrade::find()
                    ->select('id')
                    ->where(['exam_id' => (int) $model->id])
                ])
                ->execute();

            ExamGrade::deleteAll(['exam_id' => (int) $model->id]);
            $model->delete();
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            Yii::error($exception->getMessage(), __METHOD__);

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['success' => false, 'message' => 'Unable to delete exam, grade assignments, and linked subjects.'];
            }

            Yii::$app->session->setFlash('error', 'Unable to delete exam, grade assignments, and linked subjects.');
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true, 'message' => 'Exam, grade assignments, and linked subjects deleted successfully.'];
        }

        Yii::$app->session->setFlash('success', 'Exam, grade assignments, and linked subjects deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionGrades(int $id): string|Response
    {
        $exam = $this->findModel($id);
        $request = Yii::$app->request;

        $allGrades = Grade::find()
            ->orderBy(['grade' => SORT_ASC])
            ->all();

        $selectedGradeIds = ExamGrade::find()
            ->select('grade_id')
            ->where(['exam_id' => (int) $exam->id])
            ->column();

        $activeGradeIds = Grade::find()
            ->select('id')
            ->where(['status' => Grade::STATUS_ACTIVE])
            ->column();
        $activeGradeMap = array_fill_keys(array_map('intval', $activeGradeIds), true);

        if ($request->isPost) {
            $postedGradeIds = array_map('intval', (array) $request->post('grade_ids', []));
            $postedGradeIds = array_values(array_filter(array_unique($postedGradeIds), static fn(int $v): bool => $v > 0));

            $assignableGradeIds = array_values(array_filter(
                $postedGradeIds,
                static fn(int $gradeId): bool => isset($activeGradeMap[$gradeId])
            ));

            $inactiveAssignedGradeIds = array_values(array_filter(
                array_map('intval', $selectedGradeIds),
                static fn(int $gradeId): bool => !isset($activeGradeMap[$gradeId])
            ));

            $finalGradeIds = array_values(array_unique(array_merge($assignableGradeIds, $inactiveAssignedGradeIds)));

            $transaction = Yii::$app->db->beginTransaction();
            try {
                ExamGrade::deleteAll(['exam_id' => (int) $exam->id]);

                foreach ($finalGradeIds as $gradeId) {
                    $model = new ExamGrade([
                        'exam_id' => (int) $exam->id,
                        'grade_id' => $gradeId,
                    ]);

                    if (!$model->save()) {
                        throw new \RuntimeException('Unable to save selected exam grade.');
                    }

                    $this->syncExamGradeSubjects((int) $model->id, (int) $gradeId);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Exam grades updated successfully.');
                return $this->redirect(['grades', 'id' => $exam->id]);
            } catch (\Throwable $exception) {
                $transaction->rollBack();
                Yii::error($exception->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Unable to update exam grades. Please try again.');
            }
        }

        return $this->render('grades', [
            'exam' => $exam,
            'allGrades' => $allGrades,
            'selectedGradeIds' => array_map('intval', $selectedGradeIds),
            'selectedExamGradeMap' => $this->getExamGradeMap((int) $exam->id),
        ]);
    }

    public function actionSubmitMarks(): string|Response
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $academicYearId = (int) $request->post('academic_year_id', 0);
            $termId = (int) $request->post('term_id', 0);
            $gradeId = (int) $request->post('grade_id', 0);
            $examId = (int) $request->post('exam_id', 0);

            $examExists = $examId > 0
                && $academicYearId > 0
                && $termId > 0
                && $gradeId > 0
                && Exam::find()
                    ->alias('exam')
                    ->innerJoin(['exam_grade' => ExamGrade::tableName()], 'exam_grade.exam_id = exam.id')
                    ->where([
                        'exam.id' => $examId,
                        'exam.academic_year_id' => $academicYearId,
                        'exam.term_id' => $termId,
                        'exam_grade.grade_id' => $gradeId,
                    ])
                    ->exists();

            if ($examExists) {
                return $this->redirect(['student-marks', 'examId' => $examId, 'gradeId' => $gradeId]);
            }

            Yii::$app->session->setFlash('error', 'No exam found for the selected grade.');
        }

        return $this->render('submit-marks', [
            'academicYearOptions' => AcademicYear::find()
                ->select(['year', 'id'])
                ->orderBy(['year' => SORT_DESC])
                ->indexBy('id')
                ->column(),
            'termOptions' => Term::find()
                ->select(['name', 'id'])
                ->orderBy(['id' => SORT_ASC])
                ->indexBy('id')
                ->column(),
            'gradeOptions' => Grade::find()->select(['grade', 'id'])->orderBy(['grade' => SORT_ASC])->indexBy('id')->column(),
        ]);
    }

    public function actionExamOptions(int $academicYearId = 0, int $termId = 0, int $gradeId = 0): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($academicYearId <= 0 || $termId <= 0 || $gradeId <= 0) {
            return ['success' => true, 'exams' => []];
        }

        $exams = Exam::find()
            ->alias('exam')
            ->select(['exam.id', 'exam.name', 'exam.exam_no'])
            ->innerJoin(['exam_grade' => ExamGrade::tableName()], 'exam_grade.exam_id = exam.id')
            ->where([
                'exam.academic_year_id' => $academicYearId,
                'exam.term_id' => $termId,
                'exam_grade.grade_id' => $gradeId,
            ])
            ->orderBy(['exam.name' => SORT_ASC])
            ->distinct()
            ->asArray()
            ->all();

        return [
            'success' => true,
            'exams' => array_map(static function (array $row): array {
                return [
                    'id' => (int) $row['id'],
                    'label' => trim((string) ($row['name'] ?? '')) . ' (' . (string) ($row['exam_no'] ?? '') . ')',
                ];
            }, $exams),
        ];
    }

    public function actionTermOptions(int $academicYearId = 0): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($academicYearId <= 0) {
            return ['success' => true, 'terms' => []];
        }

        $terms = Exam::getTermOptions($academicYearId);

        return [
            'success' => true,
            'terms' => array_map(static function ($label, $id): array {
                return [
                    'id' => (int) $id,
                    'label' => (string) $label,
                ];
            }, $terms, array_keys($terms)),
        ];
    }

    public function actionStudentMarks(int $examId, int $gradeId): string|Response
    {
        $request = Yii::$app->request;

        $exam = Exam::find()
            ->with(['academicYear', 'term', 'examType'])
            ->where(['id' => $examId])
            ->one();

        if ($exam === null) {
            throw new NotFoundHttpException('The requested exam does not exist.');
        }

        $examGrade = ExamGrade::find()
            ->with(['grade'])
            ->where([
                'exam_id' => (int) $exam->id,
                'grade_id' => $gradeId,
            ])
            ->one();

        if ($examGrade === null) {
            Yii::$app->session->setFlash('error', 'No grade assignment found for the selected exam.');
            return $this->redirect(['submit-marks']);
        }

        $subjects = Yii::$app->db->createCommand(
            'SELECT s.id, s.code, s.name
             FROM exam_subjects es
             INNER JOIN st_subjects s ON s.id = es.subject_id
             WHERE es.exam_grade_id = :examGradeId
             ORDER BY s.name ASC',
            [':examGradeId' => (int) $examGrade->id]
        )->queryAll();

        $students = StudentEnrollment::find()
            ->alias('en')
            ->select([
                'st.id AS student_id',
                'st.upi',
                'st.first_name',
                'st.middle_name',
                'st.surname',
            ])
            ->innerJoin(['st' => 'st_students'], 'st.id = en.student_id')
            ->where([
                'en.academic_year_id' => (int) $exam->academic_year_id,
                'en.term_id' => (int) $exam->term_id,
                'en.grade_id' => (int) $examGrade->grade_id,
            ])
            ->distinct()
            ->orderBy([
                'st.first_name' => SORT_ASC,
                'st.middle_name' => SORT_ASC,
                'st.surname' => SORT_ASC,
            ])
            ->asArray()
            ->all();

        $studentIds = array_map(static fn(array $row): int => (int) $row['student_id'], $students);
        $subjectIds = array_map(static fn(array $row): int => (int) $row['id'], $subjects);
        $studentMap = array_fill_keys($studentIds, true);
        $subjectMap = array_fill_keys($subjectIds, true);

        if ($request->isPost) {
            $postedMarks = (array) $request->post('marks', []);
            $rowsToSave = [];

            foreach ($postedMarks as $studentId => $subjectMarks) {
                $studentId = (int) $studentId;
                if (!isset($studentMap[$studentId]) || !is_array($subjectMarks)) {
                    continue;
                }

                foreach ($subjectMarks as $subjectId => $value) {
                    $subjectId = (int) $subjectId;
                    if (!isset($subjectMap[$subjectId])) {
                        continue;
                    }

                    $value = trim((string) $value);
                    if ($value === '') {
                        continue;
                    }

                    if (!is_numeric($value) || (float) $value < 0) {
                        Yii::$app->session->setFlash('error', 'Marks must be numeric and zero or greater.');
                        return $this->redirect(['student-marks', 'examId' => $examId, 'gradeId' => $gradeId]);
                    }

                    $rowsToSave[] = [
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'marks' => (float) $value,
                    ];
                }
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()
                    ->delete('exam_marks', [
                        'exam_id' => (int) $exam->id,
                        'exam_grade_id' => (int) $examGrade->id,
                    ])
                    ->execute();

                $now = date('Y-m-d H:i:s');
                $userId = Yii::$app->user->id ?? null;

                foreach ($rowsToSave as $row) {
                    Yii::$app->db->createCommand()
                        ->insert('exam_marks', [
                            'exam_id' => (int) $exam->id,
                            'exam_grade_id' => (int) $examGrade->id,
                            'student_id' => (int) $row['student_id'],
                            'subject_id' => (int) $row['subject_id'],
                            'marks' => $row['marks'],
                            'created_at' => $now,
                            'updated_at' => $now,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ])
                        ->execute();
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Student marks saved successfully.');
                return $this->redirect(['student-marks', 'examId' => $examId, 'gradeId' => $gradeId]);
            } catch (\Throwable $exception) {
                $transaction->rollBack();
                Yii::error($exception->getMessage(), __METHOD__);
                Yii::$app->session->setFlash('error', 'Unable to save student marks. Please try again.');
            }
        }

        $savedMarks = Yii::$app->db->createCommand(
            'SELECT student_id, subject_id, marks
             FROM exam_marks
             WHERE exam_id = :examId AND exam_grade_id = :examGradeId',
            [
                ':examId' => (int) $exam->id,
                ':examGradeId' => (int) $examGrade->id,
            ]
        )->queryAll();

        $marksMap = [];
        foreach ($savedMarks as $row) {
            $marksMap[(int) $row['student_id']][(int) $row['subject_id']] = (string) $row['marks'];
        }

        return $this->render('student-marks', [
            'exam' => $exam,
            'examGrade' => $examGrade,
            'subjects' => $subjects,
            'students' => $students,
            'marksMap' => $marksMap,
        ]);
    }

    public function actionGradeSubjects(int $examGradeId): string|array
    {
        $examGrade = ExamGrade::find()
            ->with(['exam', 'grade'])
            ->where(['id' => $examGradeId])
            ->one();

        if ($examGrade === null) {
            throw new NotFoundHttpException('The requested exam grade does not exist.');
        }

        $request = Yii::$app->request;
        $availableSubjects = $this->getAssignableSubjects((int) $examGrade->grade_id);
        $availableSubjectIds = array_map(static fn(Subject $subject): int => (int) $subject->id, $availableSubjects);
        $availableMap = array_fill_keys($availableSubjectIds, true);

        $selectedSubjectIds = array_map('intval', Yii::$app->db->createCommand(
            'SELECT subject_id FROM exam_subjects WHERE exam_grade_id = :examGradeId',
            [':examGradeId' => (int) $examGrade->id]
        )->queryColumn());

        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $postedSubjectIds = array_map('intval', (array) $request->post('subject_ids', []));
            $postedSubjectIds = array_values(array_filter(array_unique($postedSubjectIds), static fn(int $value): bool => $value > 0));

            $finalSubjectIds = array_values(array_filter(
                $postedSubjectIds,
                static fn(int $subjectId): bool => isset($availableMap[$subjectId])
            ));

            $transaction = Yii::$app->db->beginTransaction();
            try {
                Yii::$app->db->createCommand()
                    ->delete('exam_subjects', ['exam_grade_id' => (int) $examGrade->id])
                    ->execute();

                foreach ($finalSubjectIds as $subjectId) {
                    Yii::$app->db->createCommand()
                        ->insert('exam_subjects', [
                            'exam_grade_id' => (int) $examGrade->id,
                            'subject_id' => (int) $subjectId,
                        ])
                        ->execute();
                }

                $transaction->commit();
                return ['success' => true, 'message' => 'Exam subjects updated successfully.'];
            } catch (\Throwable $exception) {
                $transaction->rollBack();
                Yii::error($exception->getMessage(), __METHOD__);
                return ['success' => false, 'message' => 'Unable to update exam subjects. Please try again.'];
            }
        }

        return $this->renderAjax('_grade_subjects_form', [
            'examGrade' => $examGrade,
            'availableSubjects' => $availableSubjects,
            'selectedSubjectIds' => $selectedSubjectIds,
        ]);
    }

    /**
     * @return int[]
     */
    private function getExamGradeMap(int $examId): array
    {
        $rows = ExamGrade::find()
            ->select(['id', 'grade_id'])
            ->where(['exam_id' => $examId])
            ->asArray()
            ->all();

        $map = [];
        foreach ($rows as $row) {
            $map[(int) $row['grade_id']] = (int) $row['id'];
        }

        return $map;
    }

    /**
     * @return Subject[]
     */
    private function getAssignableSubjects(int $gradeId): array
    {
        return Subject::find()
            ->alias('subject')
            ->innerJoin(['grade_subject' => GradeSubject::tableName()], 'grade_subject.subject_id = subject.id')
            ->where([
                'grade_subject.grade_id' => $gradeId,
                'grade_subject.status' => GradeSubject::STATUS_ACTIVE,
                'subject.status' => Subject::STATUS_ACTIVE,
            ])
            ->orderBy(['subject.name' => SORT_ASC])
            ->all();
    }

    private function syncExamGradeSubjects(int $examGradeId, int $gradeId): void
    {
        $subjects = $this->getAssignableSubjects($gradeId);

        Yii::$app->db->createCommand()
            ->delete('exam_subjects', ['exam_grade_id' => $examGradeId])
            ->execute();

        foreach ($subjects as $subject) {
            Yii::$app->db->createCommand()
                ->insert('exam_subjects', [
                    'exam_grade_id' => $examGradeId,
                    'subject_id' => (int) $subject->id,
                ])
                ->execute();
        }
    }

    protected function findModel(int $id): Exam
    {
        if (($model = Exam::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
