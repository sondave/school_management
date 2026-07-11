<?php

use app\models\ClassTeacher;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ClassTeacher $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="class-teacher-form">

    <?php $form = ActiveForm::begin([
        'id' => 'class-teacher-form',
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
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'grade_id')->dropDownList(
                    ClassTeacher::getGradeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select grade']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'teacher_id')->dropDownList(
                    ClassTeacher::getTeacherOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select teacher']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'academic_year_id')->dropDownList(
                    ClassTeacher::getAcademicYearOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select academic year']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
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
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'is_current')->dropDownList(
                    ClassTeacher::getIsCurrentOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select current status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Class Teacher'
                : '<i class="fas fa-save me-2"></i> Update Class Teacher',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$academicYearRanges = Json::htmlEncode(ClassTeacher::getAcademicYearDateRanges());
$this->registerJs(<<<JS
(function () {
    var ranges = {$academicYearRanges};
    var academicYearField = $('#classteacher-academic_year_id');
    var startDateField = $('#classteacher-start_date');

    function applyStartDateRange() {
        var selectedYearId = academicYearField.val();
        var range = ranges[selectedYearId] || null;

        if (!range) {
            startDateField.removeAttr('min').removeAttr('max');
            return;
        }

        startDateField.attr('min', range.start_date).attr('max', range.end_date);

        if (startDateField.val() && (startDateField.val() < range.start_date || startDateField.val() > range.end_date)) {
            startDateField.val('');
        }
    }

    applyStartDateRange();
    $(document).on('change', '#classteacher-academic_year_id', applyStartDateRange);
})();
JS
);
?>
