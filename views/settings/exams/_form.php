<?php

use app\models\settings\Exam;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\Exam $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="exam-form">
    <?php $form = ActiveForm::begin([
        'id' => 'exam-form',
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

    <?php if (!$model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'exam_no')->textInput([
                    'maxlength' => 50,
                    'readonly' => true,
                ])->hint('Format: academic year-term-exam type code-counter (e.g. 2026-t1-cat1)') ?>
            </div>
            <div class="col-md-6 mb-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 50, 'placeholder' => 'Exam name e.g CAT 1']) ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 50, 'placeholder' => 'Exam name e.g CAT 1']) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'academic_year_id')->dropDownList(Exam::getAcademicYearOptions(), ['class' => 'form-select', 'prompt' => 'Select academic year']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'term_id')->dropDownList(Exam::getTermOptions($model->academic_year_id), ['class' => 'form-select', 'prompt' => 'Select term']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'exam_type_id')->dropDownList(Exam::getExamTypeOptions(), ['class' => 'form-select', 'prompt' => 'Select exam type']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'start_date')->input('date') ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'end_date')->input('date') ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($model, 'status')->dropDownList(Exam::getStatusOptions(), ['class' => 'form-select']) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Save Exam' : 'Update Exam', ['class' => 'btn btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
