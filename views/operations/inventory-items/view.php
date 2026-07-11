<?php

use app\models\operations\InventoryItem;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryItem $model */
?>

<div class="inventory-item-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'accesory_type',
                'value' => $model->getAccessoryTypeLabel(),
            ],
            'name',
            'description:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (InventoryItem $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (InventoryItem $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>