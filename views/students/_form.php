<?php

use app\models\Student;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin([
        'id' => 'student-form',
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
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'upi')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'nemis_no')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'birth_cert_no')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(Student::getStatusOptions(), ['prompt' => 'Select status']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'gender_id')->dropDownList(Student::getGenderOptions(), ['prompt' => 'Select gender']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'date_of_birth')->input('date') ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'admission_date')->input('date') ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-2">
        <?= Html::submitButton('Save Student', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['students/index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
