<?php

use app\models\settings\Grade;
use app\models\settings\GradeSubject;
use app\models\settings\Subject;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\settings\GradeSubject $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="grade-subject-form">

    <?php $form = ActiveForm::begin([
        'id' => 'grade-subject-form',
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
                <?= $form->field($model, 'subject_id')->dropDownList(
                    ArrayHelper::map(Subject::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    ['class' => 'form-select', 'prompt' => 'Select subject']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'status')->dropDownList(
                    GradeSubject::getStatusOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select status']
                ) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Grade Subject'
                : '<i class="fas fa-save me-2"></i> Update Grade Subject',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
