<?php

use app\models\TeacherSubject;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TeacherSubject $model */
/** @var yii\widgets\ActiveForm $form */

$subjectOptions = empty($model->grade_id) ? [] : TeacherSubject::getSubjectOptionsByGrade((int) $model->grade_id);
?>

<div class="teacher-subject-form">

    <?php $form = ActiveForm::begin([
        'id' => 'teacher-subject-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
        'errorCssClass' => 'is-invalid',
        'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'teacher_id')->dropDownList(
                    TeacherSubject::getTeacherOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select teacher']
                ) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'grade_id')->dropDownList(
                    TeacherSubject::getGradeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select grade']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'academic_year_id')->dropDownList(
                    TeacherSubject::getAcademicYearOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select academic year']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'start_date')->input('date') ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'end_date')->input('date') ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <?= $form->field($model, 'subject_ids', [
                    'template' => "{label}\n<div id=\"teacher-subject-subjects-wrapper\">{input}</div>\n{error}",
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ])->checkboxList(
                    $subjectOptions,
                    [
                        'class' => 'teacher-subject-checkbox-list',
                        'item' => static function ($index, $label, $name, $checked, $value): string {
                            $checkbox = Html::checkbox($name, $checked, [
                                'value' => $value,
                                'class' => 'form-check-input',
                                'id' => 'teacher-subject-subject-' . $value,
                            ]);

                            return Html::tag('div',
                                Html::tag('label', $checkbox . ' <span>' . Html::encode($label) . '</span>', [
                                    'class' => 'form-check-label d-flex align-items-center gap-2',
                                    'for' => 'teacher-subject-subject-' . $value,
                                ]),
                                ['class' => 'form-check mb-2']
                            );
                        },
                    ]
                ) ?>
                <small class="text-muted d-block mt-1">Only subjects configured under Grade Subjects for the selected grade are listed.</small>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Teacher Subjects'
                : '<i class="fas fa-save me-2"></i> Update Teacher Subjects',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$selectedSubjectIds = Json::htmlEncode(array_map('intval', $model->subject_ids));
$subjectsUrl = Url::to(['teachers/teacher-subjects/subjects-by-grade']);
$this->registerJs(<<<JS
(function () {
    var gradeField = $('#teachersubject-grade_id');
    var wrapper = $('#teacher-subject-subjects-wrapper');
    var subjectsUrl = '{$subjectsUrl}';
    var selectedSubjectIds = {$selectedSubjectIds};

    function buildCheckboxHtml(options, selectedIds) {
        if (!options.length) {
            return '<div class="text-muted small py-2">No subjects have been configured for this grade.</div>';
        }

        var name = 'TeacherSubject[subject_ids][]';
        var html = '<div class="teacher-subject-checkbox-list">';
        options.forEach(function (option) {
            var inputId = 'teacher-subject-subject-' + option.id;
            var checked = selectedIds.indexOf(Number(option.id)) !== -1 ? ' checked' : '';
            html += '<div class="form-check mb-2">';
            html += '<label class="form-check-label d-flex align-items-center gap-2" for="' + inputId + '">';
            html += '<input type="checkbox" id="' + inputId + '" class="form-check-input" name="' + name + '" value="' + option.id + '"' + checked + '>';
            html += '<span>' + $('<div>').text(option.label).html() + '</span>';
            html += '</label>';
            html += '</div>';
        });
        html += '</div>';

        return html;
    }

    function loadSubjects(selectedIds) {
        var gradeId = gradeField.val();
        if (!gradeId) {
            wrapper.html('<div class="text-muted small py-2">Select a grade to load subjects.</div>');
            return;
        }

        wrapper.html('<div class="text-muted small py-2">Loading subjects...</div>');

        $.getJSON(subjectsUrl, {gradeId: gradeId}).done(function (response) {
            wrapper.html(buildCheckboxHtml(response.options || [], selectedIds || []));
        }).fail(function () {
            wrapper.html('<div class="alert alert-danger mb-0">Unable to load subjects for the selected grade.</div>');
        });
    }

    if (gradeField.val()) {
        loadSubjects(selectedSubjectIds);
    } else {
        wrapper.html('<div class="text-muted small py-2">Select a grade to load subjects.</div>');
    }

    $(document).off('change.teacher-subject-grade').on('change.teacher-subject-grade', '#teachersubject-grade_id', function () {
        loadSubjects([]);
    });
})();
JS
);
?>