<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\fees\FeePayment $model */
/** @var app\models\settings\SchoolInfo|null $schoolInfo */

$this->title = 'Payment Receipt ' . ($model->receipt_no ?? '');
$totalAllocated = 0.0;
?>

<div class="container-fluid py-4 fee-payment-receipt">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h4 class="mb-1"><?= Html::encode($schoolInfo?->name ?? 'School Management') ?></h4>
            <?php if (!empty($schoolInfo?->physical_address)): ?>
                <div><?= Html::encode((string) $schoolInfo->physical_address) ?></div>
            <?php endif; ?>
            <?php if (!empty($schoolInfo?->phone_number)): ?>
                <div>Phone: <?= Html::encode((string) $schoolInfo->phone_number) ?></div>
            <?php endif; ?>
            <?php if (!empty($schoolInfo?->email)): ?>
                <div>Email: <?= Html::encode((string) $schoolInfo->email) ?></div>
            <?php endif; ?>
        </div>
        <div class="text-end">
            <h5 class="mb-1">PAYMENT RECEIPT</h5>
            <div><strong>Receipt No:</strong> <?= Html::encode($model->receipt_no) ?></div>
            <div><strong>Date:</strong> <?= \Yii::$app->formatter->asDate($model->payment_date) ?></div>
        </div>
    </div>

    <hr>

    <div class="row mb-3">
        <div class="col-md-6">
            <div><strong>Student:</strong> <?= Html::encode($model->student?->getFullName() ?? '-') ?></div>
            <div><strong>Payment Method:</strong> <?= Html::encode($model->payment_method) ?></div>
        </div>
        <div class="col-md-6 text-md-end">
            <div><strong>Posted By:</strong> <?= Html::encode($model->createdByUser?->username ?? '-') ?></div>
            <div><strong>Posted At:</strong> <?= \Yii::$app->formatter->asDatetime($model->created_at) ?></div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fee Item</th>
                    <th class="text-end">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->allocations as $allocation): ?>
                    <?php $totalAllocated += (float) $allocation->amount; ?>
                    <tr>
                        <td><?= Html::encode($allocation->studentFeeCharge?->feeStructure?->category?->name ?? 'Fee Item') ?></td>
                        <td class="text-end">KES <?= \Yii::$app->formatter->asDecimal((float) $allocation->amount, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total Paid</th>
                    <th class="text-end">KES <?= \Yii::$app->formatter->asDecimal((float) $model->amount, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if (!empty($model->remarks)): ?>
        <div class="mt-3">
            <strong>Remarks:</strong>
            <div><?= Html::encode((string) $model->remarks) ?></div>
        </div>
    <?php endif; ?>

    <div class="mt-4 d-print-none">
        <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
    </div>
</div>

<?php $this->registerCss(<<<CSS
@media print {
  .d-print-none {
    display: none !important;
  }
  body {
    background: #fff !important;
  }
}
CSS
); ?>
