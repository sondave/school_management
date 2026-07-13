<?php

use app\controllers\settings\FeePaymentsController;
use app\models\fees\FeePayment;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\fees\FeePayment $model */
/** @var array<int, array<string,mixed>> $initialAllocations */

$chargesUrl = Url::to(['settings/fee-payments/charges']);
$initialAllocationsJson = Json::htmlEncode($initialAllocations);
?>

<div class="fee-payment-form">
    <?php $form = ActiveForm::begin([
        'id' => 'fee-payment-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'student_id')->dropDownList(FeePayment::getStudentOptions(), ['prompt' => 'Select student']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::label('Academic Year', 'payment-academic-year-id', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('academic_year_id', \Yii::$app->request->post('academic_year_id'), FeePaymentsController::getAcademicYearOptions(), ['class' => 'form-control', 'id' => 'payment-academic-year-id', 'prompt' => 'Select academic year']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::label('Term', 'payment-term-id', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('term_id', \Yii::$app->request->post('term_id'), FeePaymentsController::getTermOptions(), ['class' => 'form-control', 'id' => 'payment-term-id', 'prompt' => 'Select term']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::label('Grade', 'payment-grade-id', ['class' => 'form-label']) ?>
            <?= Html::dropDownList('grade_id', \Yii::$app->request->post('grade_id'), FeePaymentsController::getGradeOptions(), ['class' => 'form-control', 'id' => 'payment-grade-id', 'prompt' => 'Select grade']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'payment_date')->input('date') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'payment_method')->dropDownList(FeePayment::getPaymentMethodOptions(), ['prompt' => 'Select method']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0">Fee Allocations</h6>
        </div>
        <div class="card-body" id="allocations-container">
            <div class="text-muted">Select student, academic year, term and grade to load fee charges.</div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <strong>Total: KES <span id="allocations-total">0.00</span></strong>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Post Payment', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs(<<<JS
(function () {
    var chargesUrl = '{$chargesUrl}';
    var initialAllocations = {$initialAllocationsJson};

    function toNumber(value) {
        var parsed = parseFloat(value);
        return Number.isFinite(parsed) ? parsed : 0;
    }

    function formatMoney(value) {
        return toNumber(value).toFixed(2);
    }

    function updateTotal() {
        var total = 0;
        $('#allocations-container').find('.allocation-row').each(function () {
            var row = $(this);
            var checked = row.find('.allocation-checkbox').is(':checked');
            var amount = toNumber(row.find('.allocation-amount').val());
            if (checked) {
                total += amount;
            }
        });

        $('#allocations-total').text(formatMoney(total));
    }

    function renderAllocations(items) {
        if (!items || !items.length) {
            $('#allocations-container').html('<div class="text-muted">No outstanding fee charges found for this selection.</div>');
            updateTotal();
            return;
        }

        var html = '';
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered table-sm">';
        html += '<thead><tr><th style="width:70px">Pick</th><th>Fee Item</th><th>Balance</th><th style="width:180px">Amount To Pay</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var selected = true;
            var amount = toNumber(item.balance);

            if (initialAllocations[item.id]) {
                selected = String(initialAllocations[item.id].selected || '') === '1';
                amount = toNumber(initialAllocations[item.id].amount);
            }

            html += '<tr class="allocation-row">';
            html += '<td>';
            html += '<input type="checkbox" class="form-check-input allocation-checkbox" name="allocations[' + item.id + '][selected]" value="1" ' + (selected ? 'checked' : '') + '>';
            html += '</td>';
            html += '<td>' + item.label + '</td>';
            html += '<td>KES ' + formatMoney(item.balance) + '</td>';
            html += '<td>';
            html += '<input type="number" step="0.01" min="0" max="' + formatMoney(item.balance) + '" class="form-control allocation-amount" name="allocations[' + item.id + '][amount]" value="' + formatMoney(amount) + '">';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody></table></div>';

        $('#allocations-container').html(html);
        initialAllocations = {};
        updateTotal();
    }

    function loadCharges() {
        var studentId = $('#feepayment-student_id').val();
        var academicYearId = $('#payment-academic-year-id').val();
        var termId = $('#payment-term-id').val();
        var gradeId = $('#payment-grade-id').val();

        if (!studentId || !academicYearId || !termId || !gradeId) {
            $('#allocations-container').html('<div class="text-muted">Select student, academic year, term and grade to load fee charges.</div>');
            updateTotal();
            return;
        }

        $('#allocations-container').html('<div class="text-muted">Loading charges...</div>');

        $.get(chargesUrl, {
            studentId: studentId,
            academicYearId: academicYearId,
            termId: termId,
            gradeId: gradeId
        }).done(function (res) {
            if (res && res.success) {
                renderAllocations(res.items || []);
                return;
            }

            $('#allocations-container').html('<div class="text-danger">Unable to load fee charges.</div>');
            updateTotal();
        }).fail(function () {
            $('#allocations-container').html('<div class="text-danger">Unable to load fee charges.</div>');
            updateTotal();
        });
    }

    $(document).on('change', '#feepayment-student_id, #payment-academic-year-id, #payment-term-id, #payment-grade-id', loadCharges);
    $(document).on('input change', '.allocation-checkbox, .allocation-amount', updateTotal);

    if ($('#feepayment-student_id').val() && $('#payment-academic-year-id').val() && $('#payment-term-id').val() && $('#payment-grade-id').val()) {
        loadCharges();
    }
})();
JS
); ?>
