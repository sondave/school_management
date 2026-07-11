<?php

use app\models\operations\Inventory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryItem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-item-form">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-item-form',
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
                <?= $form->field($model, 'accesory_type')->dropDownList(
                    Inventory::getAccessoryTypeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select accessory type']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'placeholder' => 'Item name']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'Description']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Inventory Item'
                : '<i class="fas fa-save me-2"></i> Update Inventory Item',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>