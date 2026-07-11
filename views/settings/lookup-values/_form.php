<?php

use app\models\settings\LookupValue;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\LookupValue $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="lookup-value-form">

    <?php $form = ActiveForm::begin([
        'id' => 'lookup-value-form',
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
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'category')->dropDownList(
                    LookupValue::getCategoryOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select category']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'code')->textInput(['maxlength' => 50, 'placeholder' => 'Lookup code']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'Lookup name']) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    LookupValue::getStatusOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select status']
                ) ?>
            </div>
        </div>

    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Lookup Value'
                : '<i class="fas fa-save me-2"></i> Update Lookup Value',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
