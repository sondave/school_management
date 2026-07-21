<?php

use app\models\Student;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="student-form">

    <?php $form = ActiveForm::begin([
        'id' => 'student-form',
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
                <?= $form->field($model, 'admission_type')->radioList(Student::getAdmissionTypeOptions(), [
                    'class' => 'd-flex gap-4',
                ]) ?>
            </div>
        </div>
        <div class="col-md-6" id="transfered-from-container">
            <div class="mb-3">
                <?= $form->field($model, 'transfered_from')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'gender_id')->dropDownList(Student::getGenderOptions(), ['prompt' => 'Select gender']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
            <?= $form->field($model, 'date_of_birth')->input('date', [
                    'max' => date('Y-m-d'),
                ]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'admission_date')->input('date', [
                    'max' => date('Y-m-d'),
                ]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'upi')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'access_number')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'birth_cert_no')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="mb-3 form-check pt-2">
                <?= $form->field($model, 'has_special_needs', [
                    'template' => "{input}\n{label}\n{error}",
                    'options' => ['class' => 'form-check'],
                    'labelOptions' => ['class' => 'form-check-label'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ])->checkbox([
                    'class' => 'form-check-input',
                    'uncheck' => '0',
                    'value' => '1',
                ], false) ?>
            </div>
        </div>
    </div>

    

    


    <?php if (!$model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <?= $form->field($model, 'status')->dropDownList(Student::getStatusOptions(), ['prompt' => 'Select status']) ?>
                </div>
            </div>
            <div class="col-md-4" id="transfered-to-container">
                <div class="mb-3">
                    <?= $form->field($model, 'transfered_to')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group mt-2">
        <?= Html::submitButton('Save Student', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['students/index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    var form = $('#student-form');

    if (!form.length) {
        return;
    }

    function updateTransferedFromVisibility() {
        var selectedAdmissionType = form.find('input[name="Student[admission_type]"]:checked').val();
        var showTransferedFrom = selectedAdmissionType === 'transfer';

        $('#transfered-from-container').toggle(showTransferedFrom);
        form.find('#student-transfered_from').prop('required', showTransferedFrom);
    }

    function updateTransferedToVisibility() {
        var statusSelect = form.find('#student-status');

        if (!statusSelect.length) {
            $('#transfered-to-container').hide();
            return;
        }

        var selectedStatusLabel = (statusSelect.find('option:selected').text() || '').toLowerCase();
        var showTransferedTo = selectedStatusLabel.indexOf('transfered') !== -1 || selectedStatusLabel.indexOf('transferred') !== -1;

        $('#transfered-to-container').toggle(showTransferedTo);
        form.find('#student-transfered_to').prop('required', showTransferedTo);
    }

    form.off('change.studentAdmissionType').on('change.studentAdmissionType', 'input[name="Student[admission_type]"]', updateTransferedFromVisibility);
    form.off('change.studentStatus').on('change.studentStatus', '#student-status', updateTransferedToVisibility);

    updateTransferedFromVisibility();
    updateTransferedToVisibility();
})();
JS);
?>
