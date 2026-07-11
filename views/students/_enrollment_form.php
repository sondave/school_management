<?php

use app\models\Student;
use app\models\StudentEnrollment;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\StudentEnrollment $model */
/** @var app\models\Student $student */
?>

<div class="student-enrollment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'student-enrollment-form',
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
        <div class="col-md-4">
            <?= $form->field($model, 'academic_year_id')->dropDownList(StudentEnrollment::getAcademicYearOptions(), ['prompt' => 'Select academic year']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'grade_id')->dropDownList(StudentEnrollment::getGradeOptions(), ['prompt' => 'Select grade']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'is_current')->dropDownList([1 => 'Yes', 0 => 'No']) ?>
        </div>
    </div>

    <?= $form->field($model, 'student_id')->hiddenInput(['value' => $student->id])->label(false) ?>

    <div class="form-group mt-2">
        <?= Html::submitButton('Add Enrollment', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>