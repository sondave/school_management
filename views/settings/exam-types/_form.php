<?php

use app\models\settings\ExamType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\ExamType $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="exam-type-form">
    <?php $form = ActiveForm::begin([
        'id' => 'exam-type-form',
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

    <div class="mb-3">
        <?= $form->field($model, 'code')->textInput(['maxlength' => 50, 'placeholder' => 'Exam code e.g CAT']) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'Exam type name']) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'status')->dropDownList(
            ExamType::getStatusOptions(),
            ['class' => 'form-select']
        ) ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Save Exam Type' : 'Update Exam Type', ['class' => 'btn btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
