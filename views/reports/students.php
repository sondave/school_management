<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $rows */
/** @var int $totalStudents */
/** @var int $academicYearId */
/** @var int $termId */
/** @var array $academicYearOptions */
/** @var array $termOptions */

$this->title = 'Students By Grade Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-students">
    <div class="page-header">
        <div class="page-title">
            <h4><?= Html::encode($this->title) ?></h4>
            <h6>Total students in result: <?= number_format($totalStudents) ?></h6>
        </div>
    </div>

    <?= Html::beginForm(['reports/students'], 'get', ['class' => 'row g-3 mb-3']) ?>
    <div class="col-md-4">
        <label class="form-label">Academic Year</label>
        <?= Html::dropDownList('academicYearId', $academicYearId > 0 ? $academicYearId : null, $academicYearOptions, ['class' => 'form-select', 'prompt' => 'All years']) ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Term</label>
        <?= Html::dropDownList('termId', $termId > 0 ? $termId : null, $termOptions, ['class' => 'form-select', 'prompt' => 'All terms']) ?>
    </div>
    <div class="col-md-4 d-flex align-items-end gap-2">
        <?= Html::submitButton('Apply Filter', ['class' => 'btn btn-primary']) ?>
        <a href="<?= \yii\helpers\Url::to(['reports/students']) ?>" class="btn btn-light">Reset</a>
    </div>
    <?= Html::endForm() ?>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Grade Code</th>
                        <th>Grade</th>
                        <th>Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="4" class="text-center text-muted">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= Html::encode((string) ($row['grade_code'] ?? '-')) ?></td>
                                <td><?= Html::encode((string) ($row['grade_name'] ?? '-')) ?></td>
                                <td><?= number_format((int) ($row['students_count'] ?? 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
