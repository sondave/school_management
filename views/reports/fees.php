<?php

use app\models\fees\FeePayment;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var float $totalBilled */
/** @var float $totalCollected */
/** @var float $totalOutstanding */
/** @var float $collectionRate */
/** @var array $topBalances */
/** @var FeePayment[] $recentPayments */

$this->title = 'Fees Collection Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-fees">
    <div class="page-header">
        <div class="page-title">
            <h4><?= Html::encode($this->title) ?></h4>
            <h6>Financial summary and top balances</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3"><div class="card"><div class="card-body"><h6>Total Billed</h6><h5>KES <?= number_format($totalBilled, 2) ?></h5></div></div></div>
        <div class="col-md-3 mb-3"><div class="card"><div class="card-body"><h6>Total Collected</h6><h5>KES <?= number_format($totalCollected, 2) ?></h5></div></div></div>
        <div class="col-md-3 mb-3"><div class="card"><div class="card-body"><h6>Outstanding</h6><h5 class="text-danger">KES <?= number_format($totalOutstanding, 2) ?></h5></div></div></div>
        <div class="col-md-3 mb-3"><div class="card"><div class="card-body"><h6>Collection Rate</h6><h5><?= number_format($collectionRate, 1) ?>%</h5></div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Top Outstanding Balances</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>#</th><th>Student</th><th>UPI</th><th>Balance</th></tr></thead>
                        <tbody>
                            <?php if (empty($topBalances)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No outstanding balances found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topBalances as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= Html::encode(trim((string) (($row['first_name'] ?? '') . ' ' . ($row['middle_name'] ?? '') . ' ' . ($row['surname'] ?? '')))) ?></td>
                                        <td><?= Html::encode((string) ($row['upi'] ?? '-')) ?></td>
                                        <td>KES <?= number_format((float) ($row['total_balance'] ?? 0), 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-3">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent Fee Payments</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>Receipt</th><th>Student</th><th>Amount</th></tr></thead>
                        <tbody>
                            <?php if (empty($recentPayments)): ?>
                                <tr><td colspan="3" class="text-center text-muted">No payments found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentPayments as $payment): ?>
                                    <tr>
                                        <td><?= Html::encode((string) $payment->receipt_no) ?></td>
                                        <td><?= Html::encode((string) ($payment->student?->getFullName() ?? '-')) ?></td>
                                        <td>KES <?= number_format((float) $payment->amount, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
