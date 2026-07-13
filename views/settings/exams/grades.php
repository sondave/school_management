<?php

use app\models\settings\Exam;
use app\models\settings\Grade;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\settings\Exam $exam */
/** @var app\models\settings\Grade[] $allGrades */
/** @var int[] $selectedGradeIds */
/** @var array<int,int> $selectedExamGradeMap */

$this->title = 'Manage Exam Grades: ' . $exam->name;
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$selectedMap = array_fill_keys($selectedGradeIds, true);
?>

<div class="exam-grades-manage">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['exams/index']) ?>">Exams</a></li>
                    <li class="breadcrumb-item active">Manage Grades</li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::a('Back to Exams', ['exams/index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?php if (\Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Html::encode((string) \Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>
    <?php if (\Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode((string) \Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <?= Html::tag('div', '', ['id' => 'exam-grade-subject-message']) ?>

    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <strong>Exam No:</strong> <?= Html::encode($exam->exam_no ?: '-') ?>
                <span class="ms-3"><strong>Academic Year:</strong> <?= Html::encode($exam->getAcademicYearLabel()) ?></span>
                <span class="ms-3"><strong>Term:</strong> <?= Html::encode($exam->getTermLabel()) ?></span>
                <span class="ms-3"><strong>Type:</strong> <?= Html::encode($exam->getExamTypeLabel()) ?></span>
            </div>

            <?= Html::beginForm(['exams/grades', 'id' => $exam->id], 'post') ?>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Pick</th>
                            <th>Grade Code</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th style="width: 170px;">Manage Subjects</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allGrades)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No grades found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allGrades as $grade): ?>
                                <?php $isActive = (int) $grade->status === \app\models\settings\Grade::STATUS_ACTIVE; ?>
                                <?php $examGradeId = $selectedExamGradeMap[(int) $grade->id] ?? null; ?>
                                <tr>
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="grade_ids[]"
                                            value="<?= (int) $grade->id ?>"
                                            <?= isset($selectedMap[(int) $grade->id]) ? 'checked' : '' ?>
                                            <?= $isActive ? '' : 'disabled' ?>
                                        >
                                    </td>
                                    <td>
                                        <?php if ($examGradeId !== null): ?>
                                            <?= Html::a(
                                                Html::encode((string) $grade->code),
                                                '#',
                                                [
                                                    'class' => 'exam-grade-subjects-link',
                                                    'data-url' => Url::to(['exams/grade-subjects', 'examGradeId' => (int) $examGradeId]),
                                                ]
                                            ) ?>
                                        <?php else: ?>
                                            <?= Html::encode((string) $grade->code) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($examGradeId !== null): ?>
                                            <?= Html::a(
                                                Html::encode((string) $grade->grade),
                                                '#',
                                                [
                                                    'class' => 'exam-grade-subjects-link',
                                                    'data-url' => Url::to(['exams/grade-subjects', 'examGradeId' => (int) $examGradeId]),
                                                ]
                                            ) ?>
                                        <?php else: ?>
                                            <?= Html::encode((string) $grade->grade) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $grade->status === 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($examGradeId !== null): ?>
                                            <?= Html::a(
                                                'Manage Subjects',
                                                '#',
                                                [
                                                    'class' => 'exam-grade-subjects-link btn btn-outline-primary btn-sm',
                                                    'data-url' => Url::to(['exams/grade-subjects', 'examGradeId' => (int) $examGradeId]),
                                                ]
                                            ) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Save grade first</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <?= Html::submitButton('Save Grade Assignments', ['class' => 'btn btn-primary']) ?>
            </div>

            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<div class="modal fade" id="exam-grade-subjects-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Exam Subjects</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="exam-grade-subjects-modal-body"></div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function showExamGradeSubjectToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#exam-grade-subject-message').html(toast);
    var toastEl = document.querySelector('#exam-grade-subject-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

function openExamGradeSubjectsModal(url) {
    var modal = $('#exam-grade-subjects-modal');
    modal.find('#exam-grade-subjects-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#exam-grade-subjects-modal-body').html(html);
    }).fail(function () {
        modal.find('#exam-grade-subjects-modal-body').html('<div class="alert alert-danger">Unable to load subjects.</div>');
    });
}

$(document).on('click', '.exam-grade-subjects-link', function (e) {
    e.preventDefault();
    openExamGradeSubjectsModal($(this).data('url'));
});

$(document).on('submit', '#exam-grade-subjects-form', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            var modal = $('#exam-grade-subjects-modal');
            var successMessage = res.message || 'Exam subjects updated successfully.';

            modal.one('hidden.bs.modal', function () {
                showExamGradeSubjectToast(successMessage, 'success');
            });

            modal.modal('hide');
            return;
        }

        showExamGradeSubjectToast((res && res.message) || 'Unable to update exam subjects.', 'error');
    }).fail(function () {
        showExamGradeSubjectToast('Unable to update exam subjects.', 'error');
    });

    return false;
});
JS
); ?>
