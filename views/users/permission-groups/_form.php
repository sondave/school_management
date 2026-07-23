<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\users\PermissionGroup $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="permission-group-form">

    <?php $form = ActiveForm::begin([
        'id' => 'permission-group-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <div class="mb-3">
        <?= $form->field($model, 'name')->textInput([
            'maxlength' => true,
            'placeholder' => 'Enter permission group name',
        ]) ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Save Permission Group' : 'Update Permission Group',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
