<?php

use app\models\settings\AcademicYear;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\AcademicYear $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="academic-year-form">

    <?php $form = ActiveForm::begin([
        'id' => 'academic-year-form',
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
                <?= $form->field($model, 'year')->dropDownList(
                    AcademicYear::getYearOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select academic year']
                ) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'current')->dropDownList(
                    AcademicYear::getCurrentOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select current status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'start_date')->input('date') ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'end_date')->input('date') ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Academic Year'
                : '<i class="fas fa-save me-2"></i> Update Academic Year',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$yearRanges = Json::htmlEncode(AcademicYear::getYearDateRanges());
$this->registerJs(<<<JS
(function () {
    var ranges = {$yearRanges};
    var yearField = $('#academicyear-year');
    var startDateField = $('#academicyear-start_date');
    var endDateField = $('#academicyear-end_date');

    function applyDateRange() {
        var selectedYear = yearField.val();
        var range = ranges[selectedYear] || null;

        if (!range) {
            startDateField.removeAttr('min').removeAttr('max');
            endDateField.removeAttr('min').removeAttr('max');
            return;
        }

        startDateField.attr('min', range.start_date).attr('max', range.end_date);
        endDateField.attr('min', range.start_date).attr('max', range.end_date);

        if (startDateField.val() && (startDateField.val() < range.start_date || startDateField.val() > range.end_date)) {
            startDateField.val('');
        }

        if (endDateField.val() && (endDateField.val() < range.start_date || endDateField.val() > range.end_date)) {
            endDateField.val('');
        }
    }

    applyDateRange();
    $(document).on('change', '#academicyear-year', applyDateRange);
})();
JS
);
?>
