<?php

use app\models\settings\LookupValue;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\LookupValue $model */
?>

<div class="lookup-value-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'category',
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
                'value' => static function (LookupValue $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (LookupValue $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
