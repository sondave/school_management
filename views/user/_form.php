<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\UserProfile $profile */
/** @var array $branches */
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
            <?= $form->field($user, 'username')->textInput(['maxlength' => true, 'placeholder' => 'Enter username']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'first_name')->textInput(['maxlength' => true, 'placeholder' => 'Enter first name']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'other_names')->textInput(['maxlength' => true, 'placeholder' => 'Enter other names']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'gender')->dropDownList([
                'Male' => 'Male',
                'Female' => 'Female',
                'Other' => 'Other',
            ], ['prompt' => 'Select gender', 'class' => 'form-select']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'phone_number')->textInput(['maxlength' => true, 'placeholder' => 'Enter phone number']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Enter email']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">

            <?= $form->field($profile, 'dob')->input('date', [
                    'max' => date('Y-m-d', strtotime('-18 years')),
                ]) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'branch_id')->dropDownList($branches, ['prompt' => 'Select branch', 'class' => 'form-select']) ?>
        </div>
        <div class="col-md-4 mb-3">
            <?= $form->field($profile, 'role')->dropDownList([
                1 => 'Role 1',
                2 => 'Role 2',
                3 => 'Role 3',
            ], ['prompt' => 'Select role', 'class' => 'form-select']) ?>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::submitButton('Create User', ['class' => 'btn btn-submit']) ?>
        <?= Html::a('Cancel', ['user/index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
