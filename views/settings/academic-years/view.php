<?php

use app\models\settings\AcademicYear;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\AcademicYear $model */
?>

<div class="academic-year-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'year',
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
                'value' => static function (AcademicYear $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (AcademicYear $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
