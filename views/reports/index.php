<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $kpis */

$this->title = 'Reports';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="reports-index">
    <div class="page-header">
        <div class="page-title">
            <h4><?= Html::encode($this->title) ?></h4>
            <h6>School analytics and operational reporting</h6>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-sm-6 col-12 d-flex">
            <div class="dash-count das1 w-100">
                <div class="dash-counts">
                    <h4><?= number_format((int) ($kpis['totalStudents'] ?? 0)) ?></h4>
                    <h5>Students</h5>
                </div>
                <div class="dash-imgs"><i data-feather="users"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12 d-flex">
            <div class="dash-count das2 w-100">
                <div class="dash-counts">
                    <h4><?= number_format((int) ($kpis['activeExams'] ?? 0)) ?></h4>
                    <h5>Active Exams</h5>
                </div>
                <div class="dash-imgs"><i data-feather="activity"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-12 d-flex">
            <div class="dash-count das3 w-100">
                <div class="dash-counts">
                    <h4>KES <?= number_format((float) ($kpis['totalOutstanding'] ?? 0), 2) ?></h4>
                    <h5>Outstanding Fees</h5>
                </div>
                <div class="dash-imgs"><i data-feather="alert-triangle"></i></div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <a class="btn btn-outline-primary w-100" href="<?= \yii\helpers\Url::to(['reports/students']) ?>">Students By Grade Report</a>
        </div>
        <div class="col-md-4 mb-3">
            <a class="btn btn-outline-primary w-100" href="<?= \yii\helpers\Url::to(['reports/fees']) ?>">Fees Collection Report</a>
        </div>
        <div class="col-md-4 mb-3">
            <a class="btn btn-outline-primary w-100" href="<?= \yii\helpers\Url::to(['reports/exams']) ?>">Exam Coverage Report</a>
        </div>
    </div>
</div>
