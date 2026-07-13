<?php

use yii\helpers\Html;
use app\models\settings\Exam;

/** @var yii\web\View $this */
/** @var app\models\settings\Exam $exam */
/** @var app\models\settings\ExamGrade $examGrade */
/** @var array $subjects */
/** @var array $students */
/** @var array $marksMap */

$this->title = 'Student Marks for ' . $exam->name;
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Submit Marks', 'url' => ['submit-marks']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="student-marks-page">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['exams/index']) ?>">Exams</a></li>
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['exams/submit-marks']) ?>">Submit Marks</a></li>
                    <li class="breadcrumb-item active">Student Marks</li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::a('Back to Submit Marks', ['submit-marks'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (\Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success"><?= Html::encode((string) \Yii::$app->session->getFlash('success')) ?></div>
            <?php endif; ?>
            <?php if (\Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger"><?= Html::encode((string) \Yii::$app->session->getFlash('error')) ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <strong>Exam:</strong> <?= Html::encode($exam->name) ?><br>
                <strong>Exam No:</strong> <?= Html::encode($exam->exam_no ?: '-') ?><br>
                <strong>Grade:</strong> <?= Html::encode((string) ($examGrade->grade?->grade ?? '-')) ?><br>
                <strong>Academic Year:</strong> <?= Html::encode($exam->getAcademicYearLabel()) ?><br>
                <strong>Term:</strong> <?= Html::encode($exam->getTermLabel()) ?><br>
                <strong>Type:</strong> <?= Html::encode($exam->getExamTypeLabel()) ?>
            </div>

            <?php if (empty($subjects)): ?>
                <div class="alert alert-warning mb-0">
                    No subjects are assigned for this exam and grade. Use Manage Subjects on exam grades first.
                </div>
            <?php elseif (empty($students)): ?>
                <div class="alert alert-warning mb-0">
                    No students found in this grade for the selected academic year and term.
                </div>
            <?php else: ?>
                <?= Html::beginForm(['student-marks', 'examId' => (int) $exam->id, 'gradeId' => (int) $examGrade->grade_id], 'post') ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="min-width: 260px;">Students</th>
                                    <?php foreach ($subjects as $subject): ?>
                                        <th style="min-width: 170px;">
                                            <?= Html::encode((string) ($subject['name'] ?? '')) ?>
                                            <div class="text-muted small"><?= Html::encode((string) ($subject['code'] ?? '')) ?></div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                <?= Html::encode(trim((string) (($student['first_name'] ?? '') . ' ' . ($student['middle_name'] ?? '') . ' ' . ($student['surname'] ?? '')))) ?>
                                            </div>
                                            
                                        </td>
                                        <?php foreach ($subjects as $subject): ?>
                                            <?php
                                                $studentId = (int) ($student['student_id'] ?? 0);
                                                $subjectId = (int) ($subject['id'] ?? 0);
                                                $value = $marksMap[$studentId][$subjectId] ?? '';
                                            ?>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    name="marks[<?= $studentId ?>][<?= $subjectId ?>]"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="Enter marks"
                                                    value="<?= Html::encode((string) $value) ?>"
                                                >
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?= Html::submitButton('Save Marks', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </div>
</div>
