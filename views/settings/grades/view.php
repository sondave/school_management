<?php

use app\models\settings\Grade;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\Grade $model */
?>

<div class="grade-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'code',
            'grade',
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
                'value' => static function (Grade $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Grade $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
