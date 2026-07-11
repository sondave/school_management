<?php

use app\models\settings\Term;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\Term $model */
?>

<div class="term-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'academic_year_id',
                'value' => $model->academicYear?->year ?? '-',
            ],
            'name',
            'start_date:date',
            'end_date:date',
            [
                'attribute' => 'current',
                'value' => $model->getCurrentLabel(),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (Term $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Term $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
