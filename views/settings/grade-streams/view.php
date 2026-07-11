<?php

use app\models\settings\GradeStream;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\GradeStream $model */
?>

<div class="grade-stream-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'grade_id',
                'value' => $model->grade?->grade ?? '-',
            ],
            [
                'attribute' => 'stream_id',
                'value' => $model->stream?->stream ?? '-',
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
                'value' => static function (GradeStream $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (GradeStream $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
