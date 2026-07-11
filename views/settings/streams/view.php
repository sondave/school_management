<?php

use app\models\settings\Stream;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\Stream $model */
?>

<div class="stream-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'stream',
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
                'value' => static function (Stream $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Stream $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
