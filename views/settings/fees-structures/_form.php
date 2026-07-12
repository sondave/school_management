<?php

use app\models\settings\FeesCategory;
use app\models\settings\FeesStructure;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\FeesStructure $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fees-structure-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fees-structure-form',
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
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'academic_year_id')->dropDownList(
                FeesStructure::getAcademicYearOptions(),
                ['prompt' => '-- Select Academic Year --', 'class' => 'form-select']
            ) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'term_id')->dropDownList(
                FeesStructure::getTermOptions(),
                ['prompt' => '-- Select Term --', 'class' => 'form-select']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'grade_id')->dropDownList(
                FeesStructure::getGradeOptions(),
                ['prompt' => '-- Select Grade --', 'class' => 'form-select']
            ) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'category_id')->dropDownList(
                FeesCategory::getActiveOptions(),
                ['prompt' => '-- Select Category --', 'class' => 'form-select']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'placeholder' => 'Amount']) ?>
        </div>
        <div class="col-md-6 mb-3">
            <?= $form->field($model, 'status')->dropDownList(
                FeesStructure::getStatusOptions(),
                ['class' => 'form-select']
            ) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Save Fee Structure' : 'Update Fee Structure', ['class' => 'btn btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
