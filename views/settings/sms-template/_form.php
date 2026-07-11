<?php

use app\models\settings\SmsTemplate;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var app\models\settings\SmsTemplate $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="sms-template-form">

    <?php $form = ActiveForm::begin([
        'id' => 'sms-template-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
        'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
        'errorCssClass' => 'is-invalid',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <div class="mb-3">
            
        <?= $form->field($model, 'name')->dropDownList(
            SmsTemplate::getGroupDropdownList(), // Loads our PHP array map data natively
            [
                'prompt' => '-- Select Alert Name --', // Renders a blank fallback choice at the top
                'class' => 'form-select',                  // Standard Bootstrap 5 dropdown styling class
            ]
        ) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => 'Optional description e.g Alert for student school fees arears']) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'template')->textarea(['rows' => 3, 'placeholder' => 'Enter SMS template body, e.g Dear {name}, your school fees arears of {amount} has been processed successfully.']) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'status')->dropDownList([
            1 => 'Active',
            0 => 'Inactive',
        ], ['class' => 'form-select']) ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Save Template' : 'Update Template', ['class' => 'btn btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
