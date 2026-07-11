<?php

use app\models\settings\Grade;
use app\models\settings\GradeStream;
use app\models\settings\Stream;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\GradeStream $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="grade-stream-form">

    <?php $form = ActiveForm::begin([
        'id' => 'grade-stream-form',
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
                <?= $form->field($model, 'grade_id')->dropDownList(
                    ArrayHelper::map(Grade::find()->orderBy(['grade' => SORT_ASC])->all(), 'id', 'grade'),
                    ['class' => 'form-select', 'prompt' => 'Select grade']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'stream_id')->dropDownList(
                    ArrayHelper::map(Stream::find()->orderBy(['stream' => SORT_ASC])->all(), 'id', 'stream'),
                    ['class' => 'form-select', 'prompt' => 'Select stream']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    GradeStream::getStatusOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Grade Stream'
                : '<i class="fas fa-save me-2"></i> Update Grade Stream',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
