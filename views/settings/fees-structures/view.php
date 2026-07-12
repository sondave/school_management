<?php

use app\models\settings\FeesStructure;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\FeesStructure $model */
?>

<div class="fees-structure-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'academic_year_id',
                'value' => $model->academicYear?->year ?? '-',
            ],
            [
                'attribute' => 'term_id',
                'value' => $model->term?->name ?? '-',
            ],
            [
                'attribute' => 'grade_id',
                'value' => $model->grade?->grade ?? '-',
            ],
            [
                'attribute' => 'category_id',
                'value' => $model->category?->name ?? '-',
            ],
            [
                'attribute' => 'amount',
                'value' => number_format((float) $model->amount, 2),
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
                'value' => static function (FeesStructure $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (FeesStructure $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
