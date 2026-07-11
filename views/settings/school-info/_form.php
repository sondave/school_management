<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\SchoolInfo $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="school-info-form">

    <?php $form = ActiveForm::begin([
        'id' => 'school-info-form',
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
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'School name']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'school_type')->dropDownList(
                    \app\models\settings\SchoolInfo::getSchoolTypeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select school type']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'phone_number')->textInput(['maxlength' => 30, 'placeholder' => 'Phone number']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'placeholder' => 'Optional email']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'website')->textInput(['maxlength' => 255, 'placeholder' => 'Optional website']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'county')->dropDownList(
                    \app\models\settings\SchoolInfo::getKenyaCountyOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select county']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'physical_address')->textInput(['maxlength' => 255, 'placeholder' => 'Physical address']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'postal_address')->textInput(['maxlength' => 255, 'placeholder' => 'Optional postal address']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'motto')->textInput(['maxlength' => 255, 'placeholder' => 'Optional motto']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'mission')->textarea(['rows' => 3, 'placeholder' => 'Optional mission']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save School Info'
                : '<i class="fas fa-save me-2"></i> Update School Info',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
