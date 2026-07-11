<?php

use app\models\settings\Stream;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\Stream $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="stream-form">

    <?php $form = ActiveForm::begin([
        'id' => 'stream-form',
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
                <?= $form->field($model, 'stream')->textInput(['maxlength' => 255, 'placeholder' => 'Stream name']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    Stream::getStatusOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Stream'
                : '<i class="fas fa-save me-2"></i> Update Stream',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
