<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var array $academicYearOptions */
/** @var array $termOptions */
/** @var array $gradeOptions */

$this->title = 'Submit Marks';
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$termOptionsUrl = Url::to(['exams/term-options']);
$examOptionsUrl = Url::to(['exams/exam-options']);
?>

<div class="submit-marks-page">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['exams/index']) ?>">Exams</a></li>
                    <li class="breadcrumb-item active">Submit Marks</li>
                </ul>
            </div>
        </div>
    </div>

    <?php if (\Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode((string) \Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form id="submit-marks-form" method="post" action="<?= Url::to(['exams/submit-marks']) ?>">
                <input type="hidden" name="_csrf" value="<?= Html::encode((string) \Yii::$app->request->csrfToken) ?>">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="academic-year-id">Academic Year</label>
                        <?= Html::dropDownList('academic_year_id', null, $academicYearOptions, [
                            'class' => 'form-select',
                            'prompt' => 'Select academic year',
                            'id' => 'academic-year-id',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="term-id">Term</label>
                        <?= Html::dropDownList('term_id', null, [], [
                            'class' => 'form-select',
                            'prompt' => 'Select term',
                            'id' => 'term-id',
                            'disabled' => true,
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="grade-id">Grade</label>
                        <?= Html::dropDownList('grade_id', null, $gradeOptions, [
                            'class' => 'form-select',
                            'prompt' => 'Select grade',
                            'id' => 'grade-id',
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="exam-id">Exam</label>
                        <select id="exam-id" name="exam_id" class="form-select" disabled>
                            <option value="">Select exam</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <!-- <?= Html::button('Load Exams', ['class' => 'btn btn-outline-primary', 'id' => 'load-exams-button']) ?> -->
                    <?= Html::submitButton('Open Student Marks', ['class' => 'btn btn-outline-primary']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->registerJs(
    'var termOptionsUrl = ' . Json::htmlEncode($termOptionsUrl) . ";\n"
    . 'var examOptionsUrl = ' . Json::htmlEncode($examOptionsUrl) . ";\n"
    . <<<'JS'
function loadExamOptions(showAlertWhenEmpty) {
    var academicYearId = $('#academic-year-id').val();
    var termId = $('#term-id').val();
    var gradeId = $('#grade-id').val();
    var examSelect = $('#exam-id');

    examSelect.prop('disabled', true).html('<option value="">Loading...</option>');

    if (!academicYearId || !termId || !gradeId) {
        examSelect.html('<option value="">Select exam</option>');
        return;
    }

    $.get(examOptionsUrl, {
        academicYearId: academicYearId,
        termId: termId,
        gradeId: gradeId
    }).done(function (res) {
        var exams = (res && res.exams) ? res.exams : [];
        var html = '<option value="">Select exam</option>';

        if (!exams.length) {
            examSelect.html(html).prop('disabled', true);
            if (showAlertWhenEmpty) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('No exam found', 'No exam found for the selected grade.', 'warning');
                } else {
                    alert('No exam found for the selected grade.');
                }
            }
            return;
        }

        exams.forEach(function (exam) {
            html += '<option value="' + exam.id + '">' + exam.label + '</option>';
        });

        examSelect.html(html).prop('disabled', false);
    }).fail(function () {
        examSelect.html('<option value="">Select exam</option>').prop('disabled', true);
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'Unable to load exams.', 'error');
        } else {
            alert('Unable to load exams.');
        }
    });
}

function loadTermOptions() {
    var academicYearId = $('#academic-year-id').val();
    var termSelect = $('#term-id');

    termSelect.prop('disabled', true).html('<option value="">Loading...</option>');
    $('#exam-id').prop('disabled', true).html('<option value="">Select exam</option>');

    if (!academicYearId) {
        termSelect.html('<option value="">Select term</option>');
        return;
    }

    $.get(termOptionsUrl, {
        academicYearId: academicYearId
    }).done(function (res) {
        var terms = (res && res.terms) ? res.terms : [];
        var html = '<option value="">Select term</option>';

        terms.forEach(function (term) {
            html += '<option value="' + term.id + '">' + term.label + '</option>';
        });

        termSelect.html(html).prop('disabled', false);
    }).fail(function () {
        termSelect.html('<option value="">Select term</option>');
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error', 'Unable to load terms.', 'error');
        } else {
            alert('Unable to load terms.');
        }
    });
}

$(document).on('change', '#academic-year-id, #term-id, #grade-id', function () {
    loadExamOptions(true);
});

$(document).on('change', '#academic-year-id', function () {
    loadTermOptions();
});

$(document).on('click', '#load-exams-button', function (e) {
    e.preventDefault();
    loadExamOptions(true);
});

$(function () {
    if ($('#academic-year-id').val()) {
        loadTermOptions();
    }
});

$(document).on('submit', '#submit-marks-form', function (e) {
    var examId = $('#exam-id').val();
    if (!examId) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire('No exam found', 'No exam found for the selected grade.', 'warning');
        } else {
            alert('No exam found for the selected grade.');
        }
        return false;
    }
});
JS
); ?>
