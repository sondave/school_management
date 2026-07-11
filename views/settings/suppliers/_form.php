<?php

use app\models\settings\Supplier;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\Supplier $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="supplier-form">

    <?php $form = ActiveForm::begin([
        'id' => 'supplier-form',
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
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'Supplier name']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'source_type')->dropDownList(
                    Supplier::getSourceTypeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select source type']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => 30, 'placeholder' => 'Phone']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'placeholder' => 'Email']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'address')->textInput(['maxlength' => 255, 'placeholder' => 'Address']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Supplier'
                : '<i class="fas fa-save me-2"></i> Update Supplier',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>