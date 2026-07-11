<?php

use app\models\Student;
use app\models\StudentParent;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\StudentParent $model */
/** @var app\models\Student $student */
?>

<div class="student-parent-form">
    <?php $form = ActiveForm::begin([
        'id' => 'student-parent-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
    ]); ?>

    <div class="alert alert-info mb-3">
        <strong>Student:</strong> <?= Html::encode($student->getFullName()) ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'parent_id')->dropDownList(StudentParent::getParentOptions(), ['prompt' => 'Select parent']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'relationship')->dropDownList(StudentParent::getRelationshipOptions(), ['prompt' => 'Select relationship']) ?>
        </div>
    </div>

    <?= $form->field($model, 'student_id')->hiddenInput(['value' => $student->id])->label(false) ?>

    <div class="form-group mt-2">
        <?= Html::submitButton('Add Parent', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>