<?php

use app\models\settings\Exam;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $statusMap */
/** @var array $gradeCoverage */
/** @var Exam[] $recentExams */

$this->title = 'Exam Coverage Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-exams">
    <div class="page-header">
        <div class="page-title">
            <h4><?= Html::encode($this->title) ?></h4>
            <h6>Status summary and grade exam coverage</h6>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>Active</h6><h5><?= number_format((int) ($statusMap[Exam::STATUS_ACTIVE] ?? 0)) ?></h5></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>Completed</h6><h5><?= number_format((int) ($statusMap[Exam::STATUS_COMPLETED] ?? 0)) ?></h5></div></div></div>
        <div class="col-md-4"><div class="card"><div class="card-body"><h6>Canceled</h6><h5><?= number_format((int) ($statusMap[Exam::STATUS_CANCELED] ?? 0)) ?></h5></div></div></div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Exam Coverage By Grade</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>#</th><th>Grade Code</th><th>Grade</th><th>Exams Assigned</th></tr></thead>
                        <tbody>
                            <?php if (empty($gradeCoverage)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No grade coverage records found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($gradeCoverage as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= Html::encode((string) ($row['grade_code'] ?? '-')) ?></td>
                                        <td><?= Html::encode((string) ($row['grade_name'] ?? '-')) ?></td>
                                        <td><?= number_format((int) ($row['exams_count'] ?? 0)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Recent Exams</h5></div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead><tr><th>Exam</th><th>Year</th><th>Term</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if (empty($recentExams)): ?>
                                <tr><td colspan="4" class="text-center text-muted">No exams found.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentExams as $exam): ?>
                                    <tr>
                                        <td><?= Html::encode((string) $exam->name) ?></td>
                                        <td><?= Html::encode((string) $exam->getAcademicYearLabel()) ?></td>
                                        <td><?= Html::encode((string) $exam->getTermLabel()) ?></td>
                                        <td><?= Html::encode((string) $exam->getStatusLabel()) ?></td>
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
