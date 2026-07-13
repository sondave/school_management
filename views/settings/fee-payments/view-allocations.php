<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\fees\FeePayment $model */

$totalAllocated = 0.0;
?>

<div class="fee-payment-allocations-view">
    <div class="mb-3">
        <div><strong>Receipt No:</strong> <?= Html::encode($model->receipt_no) ?></div>
        <div><strong>Student:</strong> <?= Html::encode($model->student?->getFullName() ?? '-') ?></div>
        <div><strong>Payment Date:</strong> <?= \Yii::$app->formatter->asDate($model->payment_date) ?></div>
        <div><strong>Payment Method:</strong> <?= Html::encode($model->payment_method) ?></div>
        <div><strong>Total Paid:</strong> KES <?= \Yii::$app->formatter->asDecimal((float) $model->amount, 2) ?></div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>Fee Item</th>
                    <th class="text-end">Allocated Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($model->allocations)): ?>
                    <tr>
                        <td colspan="2" class="text-center text-muted">No allocations found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($model->allocations as $allocation): ?>
                        <?php $totalAllocated += (float) $allocation->amount; ?>
                        <tr>
                            <td><?= Html::encode($allocation->studentFeeCharge?->feeStructure?->category?->name ?? 'Fee Item') ?></td>
                            <td class="text-end">KES <?= \Yii::$app->formatter->asDecimal((float) $allocation->amount, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th class="text-end">KES <?= \Yii::$app->formatter->asDecimal($totalAllocated, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
