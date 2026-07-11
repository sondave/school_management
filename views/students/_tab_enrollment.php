<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
/** @var app\models\StudentEnrollment $enrollmentModel */
?>

<div class="student-enrollment-tab">
    <h5 class="mb-3">Enrollment History</h5>

    <div class="table-responsive mb-4">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Academic Year</th>
                <th>Grade</th>
                <th>Current</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($model->enrollments)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted">No enrollment records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($model->enrollments as $enrollment): ?>
                    <tr>
                        <td><?= Html::encode($enrollment->getAcademicYearLabel()) ?></td>
                        <td><?= Html::encode($enrollment->getGradeLabel()) ?></td>
                        <td>
                            <?php if ((int) $enrollment->is_current === 1): ?>
                                <span class="badge bg-success">Current</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ((int) $enrollment->is_current !== 1): ?>
                                <?= Html::a('Set Current', ['students/set-current-enrollment', 'id' => $model->id, 'enrollmentId' => $enrollment->id], [
                                    'class' => 'btn btn-sm btn-outline-primary me-1',
                                    'data' => [
                                        'method' => 'post',
                                        'confirm' => 'Set this enrollment as current?',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                            <?= Html::a('Delete', ['students/delete-enrollment', 'id' => $model->id, 'enrollmentId' => $enrollment->id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Delete this enrollment?',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
