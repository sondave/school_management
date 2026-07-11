<?php

use app\models\settings\GradeSubject;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\GradeSubject $model */
?>

<div class="grade-subject-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'grade_id',
                'value' => $model->grade?->grade ?? '-',
            ],
            [
                'attribute' => 'subject_id',
                'value' => $model->subject?->name ?? '-',
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (GradeSubject $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (GradeSubject $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
