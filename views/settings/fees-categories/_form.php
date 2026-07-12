<?php

use app\models\settings\FeesCategory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\FeesCategory $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fees-category-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fees-category-form',
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
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'Fee category name']) ?>
    </div>

    <div class="mb-3 form-check">
        <?= $form->field($model, 'is_optional')->checkbox([
            'class' => 'form-check-input',
            'label' => false,
            'uncheck' => 0,
        ]) ?>
        <label class="form-check-label" for="feescategory-is_optional">Optional fee</label>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'status')->dropDownList(
            FeesCategory::getStatusOptions(),
            ['class' => 'form-select']
        ) ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->isNewRecord ? 'Save Fee Category' : 'Update Fee Category', ['class' => 'btn btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
