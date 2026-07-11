<?php

use app\models\operations\Inventory;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\operations\Inventory $model */
?>

<div class="inventory-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'accesory_type',
                'value' => $model->getAccessoryTypeLabel(),
            ],
            [
                'attribute' => 'inventory_item_id',
                'value' => $model->getInventoryItemLabel(),
            ],
            [
                'attribute' => 'supplier_id',
                'value' => $model->getSupplierLabel(),
            ],
            'remarks:ntext',
            'quantity',
            'received_on:date',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (Inventory $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Inventory $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>