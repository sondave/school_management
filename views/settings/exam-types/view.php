<?php

use app\models\settings\ExamType;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\ExamType $model */
?>

<div class="exam-type-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'code',
            'name',
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
                'value' => static function (ExamType $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (ExamType $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
