<?php

use app\models\settings\Term;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\Term $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="term-form">

    <?php $form = ActiveForm::begin([
        'id' => 'term-form',
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
                <?= $form->field($model, 'academic_year_id')->dropDownList(
                    Term::getAcademicYearOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select academic year']
                ) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'name')->dropDownList(
                    Term::getNameOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select term']
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

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'current')->dropDownList(
                    Term::getCurrentOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select current status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Term'
                : '<i class="fas fa-save me-2"></i> Update Term',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$academicYearRanges = Json::htmlEncode(Term::getAcademicYearDateRanges());
$this->registerJs(<<<JS
(function () {
    var ranges = {$academicYearRanges};
    var academicYearField = $('#term-academic_year_id');
    var startDateField = $('#term-start_date');
    var endDateField = $('#term-end_date');

    function applyDateRange() {
        var selectedYearId = academicYearField.val();
        var range = ranges[selectedYearId] || null;

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
    $(document).on('change', '#term-academic_year_id', applyDateRange);
})();
JS
);
?>
