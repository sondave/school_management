<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\UserProfile $profile */
?>

<div class="user-form">
    <?php $form = ActiveForm::begin([
        'id' => 'user-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => true,
        'validateOnSubmit' => true,
        'validationUrl' => ['user/create'],
        'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <?= $form->errorSummary([$user, $profile], ['class' => 'alert alert-danger']) ?>

    <div class="row">
        <div class="col-md-4 mb-3">
            <?= $form->field($user, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Enter username', 'oninput' => 'this.value = this.value.toLowerCase()']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($user, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Enter email', 'oninput' => 'this.value = this.value.toLowerCase()']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'first_name')->textInput(['maxlength' => true, 'placeholder' => 'Enter first name', 'oninput' => 'this.value = this.value.replace(/\b\w/g, function(char){ return char.toUpperCase(); })']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'other_names')->textInput(['maxlength' => true, 'placeholder' => 'Enter other names', 'oninput' => 'this.value = this.value.replace(/\b\w/g, function(char){ return char.toUpperCase(); })']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'gender')->dropDownList([
                'Male' => 'Male',
                'Female' => 'Female',
                'Other' => 'Other',
            ], ['prompt' => 'Select gender', 'class' => 'form-select']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'Enter phone number']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">

            <?= $form->field($profile, 'dob')->input('date', [
                    'max' => date('Y-m-d', strtotime('-18 years')),
                ]) ?>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::submitButton('Create User', ['class' => 'btn btn-submit']) ?>
        <?= Html::a('Cancel', ['user/index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
