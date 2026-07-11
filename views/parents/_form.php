<?php

use app\models\Parents;
use app\models\settings\SchoolInfo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Parents $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="parent-form">

    <?php $form = ActiveForm::begin([
        'id' => 'parent-form',
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
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => 100, 'placeholder' => 'First name']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'other_names')->textInput(['maxlength' => 150, 'placeholder' => 'Other names']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'gender')->dropDownList(
                    Parents::getGenderOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select gender']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'national_id')->textInput(['maxlength' => 20, 'placeholder' => 'Optional national ID']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'date_of_birth')->input('date') ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'phone_no')->textInput(['maxlength' => 20, 'placeholder' => 'Phone number']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'alternate_phone_no')->textInput(['maxlength' => 20, 'placeholder' => 'Optional alternate phone']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'placeholder' => 'Optional email']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'county')->dropDownList(
                    SchoolInfo::getKenyaCountyOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select county']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'physical_address')->textInput(['maxlength' => 255, 'placeholder' => 'Physical address']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    Parents::getStatusOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Parent'
                : '<i class="fas fa-save me-2"></i> Update Parent',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
