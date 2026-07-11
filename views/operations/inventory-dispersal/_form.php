<?php

use app\models\operations\InventoryDispersal;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryDispersal $model */
/** @var yii\widgets\ActiveForm $form */

$inventoryItemOptions = empty($model->accesory_type)
    ? []
    : InventoryDispersal::getInventoryItemOptions((string) $model->accesory_type);

$termOptions = empty($model->academic_year_id)
    ? []
    : InventoryDispersal::getTermOptions((int) $model->academic_year_id);

$studentOptions = (empty($model->grade_id) && (string) $model->dispersed_to === InventoryDispersal::DISPERSED_TO_STUDENT)
    ? InventoryDispersal::getStudentOptions(null)
    : InventoryDispersal::getStudentOptions((int) $model->grade_id);
?>

<div class="inventory-dispersal-form">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-dispersal-form',
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
                    InventoryDispersal::getAccessoryTypeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select accessory type']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'inventory_item_id')->dropDownList(
                    $inventoryItemOptions,
                    ['class' => 'form-select', 'prompt' => 'Select inventory item']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'dispersed_to')->dropDownList(
                    InventoryDispersal::getDispersedToOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select dispersed target']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" id="teacher-field-wrap">
            <div class="mb-3">
                <?= $form->field($model, 'teacher_id')->dropDownList(
                    InventoryDispersal::getTeacherOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select teacher']
                ) ?>
            </div>
        </div>
        <div class="col-md-4" id="grade-field-wrap">
            <div class="mb-3">
                <?= $form->field($model, 'grade_id')->dropDownList(
                    InventoryDispersal::getGradeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select grade']
                ) ?>
            </div>
        </div>
        <div class="col-md-4" id="student-field-wrap">
            <div class="mb-3">
                <?= $form->field($model, 'student_id')->dropDownList(
                    $studentOptions,
                    ['class' => 'form-select', 'prompt' => 'Select student']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'academic_year_id')->dropDownList(
                    InventoryDispersal::getAcademicYearOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select academic year']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'term_id')->dropDownList(
                    $termOptions,
                    ['class' => 'form-select', 'prompt' => 'Select term']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'dispersed_on')->input('date') ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <?= $form->field($model, 'qty_dispersed')->input('number', ['min' => 0]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3 pt-4">
                <?= $form->field($model, 'is_to_be_returned')->checkbox(['label' => 'Is To Be Returned']) ?>
            </div>
        </div>
        <div class="col-md-3" id="returned-on-wrap">
            <div class="mb-3">
                <?= $form->field($model, 'returned_on')->input('date') ?>
            </div>
        </div>
        <div class="col-md-3" id="qty-returned-wrap">
            <div class="mb-3">
                <?= $form->field($model, 'qty_returned')->input('number', ['min' => 0]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4" id="missplaced-wrap">
            <div class="mb-3" >
                <?= $form->field($model, 'missplaced')->input('number', ['readonly' => true, 'min' => 0]) ?>
            </div>
        </div>
        <div class="col-md-8">
            <div class="mb-3">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => 255, 'placeholder' => 'Remarks']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Dispersal'
                : '<i class="fas fa-save me-2"></i> Update Dispersal',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$itemsByTypeUrl = Url::to(['operations/inventory-dispersal/items-by-accessory-type']);
$termsByYearUrl = Url::to(['operations/inventory-dispersal/terms-by-academic-year']);
$studentsByGradeUrl = Url::to(['operations/inventory-dispersal/students-by-grade']);

$selectedInventoryItemId = Json::htmlEncode((int) ($model->inventory_item_id ?? 0));
$selectedTermId = Json::htmlEncode((int) ($model->term_id ?? 0));
$selectedStudentId = Json::htmlEncode((int) ($model->student_id ?? 0));

$dispersedToTeacher = Json::htmlEncode(InventoryDispersal::DISPERSED_TO_TEACHER);
$dispersedToStudent = Json::htmlEncode(InventoryDispersal::DISPERSED_TO_STUDENT);

$initialDispersedTo = Json::htmlEncode((string) ($model->dispersed_to ?? ''));
$initialIsToBeReturned = Json::htmlEncode((int) ($model->is_to_be_returned ?? 0));
$initialQtyDispersed = Json::htmlEncode((int) ($model->qty_dispersed ?? 0));
$initialQtyReturned = Json::htmlEncode((int) ($model->qty_returned ?? 0));

$this->registerJs(<<<JS
(function () {
    var accessoryField = $('#inventorydispersal-accesory_type');
    var itemField = $('#inventorydispersal-inventory_item_id');
    var dispersedToField = $('#inventorydispersal-dispersed_to');
    var teacherField = $('#inventorydispersal-teacher_id');
    var gradeField = $('#inventorydispersal-grade_id');
    var studentField = $('#inventorydispersal-student_id');
    var academicYearField = $('#inventorydispersal-academic_year_id');
    var termField = $('#inventorydispersal-term_id');
    var isToBeReturnedField = $('#inventorydispersal-is_to_be_returned');
    var returnedOnField = $('#inventorydispersal-returned_on');
    var qtyDispersedField = $('#inventorydispersal-qty_dispersed');
    var qtyReturnedField = $('#inventorydispersal-qty_returned');
    var missplacedField = $('#inventorydispersal-missplaced');

    var selectedInventoryItemId = {$selectedInventoryItemId};
    var selectedTermId = {$selectedTermId};
    var selectedStudentId = {$selectedStudentId};
    var dispersedToTeacher = {$dispersedToTeacher};
    var dispersedToStudent = {$dispersedToStudent};
    var initialDispersedTo = {$initialDispersedTo};
    var initialIsToBeReturned = Number({$initialIsToBeReturned}) === 1;
    var initialQtyDispersed = Number({$initialQtyDispersed}) || 0;
    var initialQtyReturned = Number({$initialQtyReturned}) || 0;

    function buildOptions(items, selectedId, prompt) {
        var html = '<option value="">' + prompt + '</option>';
        items.forEach(function (item) {
            var selected = Number(selectedId) === Number(item.id) ? ' selected' : '';
            html += '<option value="' + item.id + '"' + selected + '>' + $('<div>').text(item.label).html() + '</option>';
        });
        return html;
    }

    function loadInventoryItems(selectedId) {
        var accessoryType = accessoryField.val();
        if (!accessoryType) {
            itemField.html('<option value="">Select inventory item</option>').val('');
            return;
        }

        $.getJSON('{$itemsByTypeUrl}', {accesoryType: accessoryType})
            .done(function (response) {
                var items = (response && response.options) ? response.options : [];
                itemField.html(buildOptions(items, selectedId, 'Select inventory item'));
            })
            .fail(function () {
                itemField.html('<option value="">Select inventory item</option>').val('');
            });
    }

    function loadTerms(selectedId) {
        var academicYearId = academicYearField.val();
        if (!academicYearId) {
            termField.html('<option value="">Select term</option>').val('');
            return;
        }

        $.getJSON('{$termsByYearUrl}', {academicYearId: academicYearId})
            .done(function (response) {
                var items = (response && response.options) ? response.options : [];
                termField.html(buildOptions(items, selectedId, 'Select term'));
            })
            .fail(function () {
                termField.html('<option value="">Select term</option>').val('');
            });
    }

    function loadStudents(selectedId) {
        var gradeId = gradeField.val() || 0;

        $.getJSON('{$studentsByGradeUrl}', {gradeId: gradeId})
            .done(function (response) {
                var items = (response && response.options) ? response.options : [];
                studentField.html(buildOptions(items, selectedId, 'Select student'));
            })
            .fail(function () {
                studentField.html('<option value="">Select student</option>').val('');
            });
    }

    function toggleTargetFields() {
        var dispersedTo = dispersedToField.val();
        var isTeacher = dispersedTo === dispersedToTeacher;
        var isStudent = dispersedTo === dispersedToStudent;

        $('#teacher-field-wrap').toggle(isTeacher);
        teacherField.prop('disabled', !isTeacher);
        if (!isTeacher) {
            teacherField.val('');
        }

        $('#grade-field-wrap').toggle(isStudent);
        $('#student-field-wrap').toggle(isStudent);
        gradeField.prop('disabled', !isStudent);
        studentField.prop('disabled', !isStudent);

        if (!isStudent) {
            gradeField.val('');
            studentField.val('');
            studentField.html('<option value="">Select student</option>');
            return;
        }

        loadStudents(selectedStudentId);
    }

    function recalculateMissplaced() {
        var dispersed = Number(qtyDispersedField.val()) || 0;
        var returned = Number(qtyReturnedField.val()) || 0;
        var missplaced = Math.max(0, dispersed - returned);
        missplacedField.val(missplaced);
    }

    function toggleReturnFields() {
        var checked = isToBeReturnedField.is(':checked');

        $('#returned-on-wrap').toggle(checked);
        $('#qty-returned-wrap').toggle(checked);
        $('#missplaced-wrap').toggle(checked);

        returnedOnField.prop('disabled', !checked);
        qtyReturnedField.prop('disabled', !checked);
        missplacedField.prop('disabled', !checked);

        if (!checked) {
            returnedOnField.val('');
            qtyReturnedField.val(0);
            missplacedField.val(0);
        }

        recalculateMissplaced();
    }

    $(document).off('change.inventory-dispersal').on('change.inventory-dispersal', '#inventorydispersal-accesory_type', function () {
        loadInventoryItems(0);
    });

    $(document).off('change.inventory-dispersal-year').on('change.inventory-dispersal-year', '#inventorydispersal-academic_year_id', function () {
        loadTerms(0);
    });

    $(document).off('change.inventory-dispersal-grade').on('change.inventory-dispersal-grade', '#inventorydispersal-grade_id', function () {
        loadStudents(0);
    });

    $(document).off('change.inventory-dispersal-target').on('change.inventory-dispersal-target', '#inventorydispersal-dispersed_to', function () {
        selectedStudentId = 0;
        toggleTargetFields();
    });

    $(document).off('change.inventory-dispersal-return').on('change.inventory-dispersal-return', '#inventorydispersal-is_to_be_returned', function () {
        toggleReturnFields();
    });

    $(document).off('input.inventory-dispersal-qty').on('input.inventory-dispersal-qty', '#inventorydispersal-qty_dispersed, #inventorydispersal-qty_returned', function () {
        recalculateMissplaced();
    });

    if (accessoryField.val()) {
        loadInventoryItems(selectedInventoryItemId);
    }

    if (academicYearField.val()) {
        loadTerms(selectedTermId);
    }

    if (initialDispersedTo) {
        dispersedToField.val(initialDispersedTo);
    }
    toggleTargetFields();

    isToBeReturnedField.prop('checked', initialIsToBeReturned);
    qtyDispersedField.val(initialQtyDispersed);
    qtyReturnedField.val(initialQtyReturned);
    toggleReturnFields();
})();
JS
);
?>