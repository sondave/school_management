<?php

use app\models\Payment;
use yii\helpers\Html;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var string $fromDate */
/** @var string $toDate */
/** @var array $customerStats */
/** @var array $loanStats */
/** @var float $principalTotal */
/** @var float $interestTotal */
/** @var float $receivableTotal */
/** @var float $totalRepaid */
/** @var float $outstandingTotal */
/** @var float $collectionRate */
/** @var float $registrationFee */
/** @var float $registrationCollected */
/** @var array $monthlyCollections */
/** @var array $topOverdueLoans */
/** @var array $topPayingCustomers */
/** @var Payment[] $recentRepayments */

$this->title = 'Dashboard';

?>

<div class="page-header">
    <div class="page-title mb-2">
        <h4>Loan Management Dashboard</h4>
        <h6>Snapshot of portfolio health, collections, and customer activation</h6>
    </div>
</div>



