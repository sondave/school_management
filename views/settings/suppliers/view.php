<?php

use app\models\settings\Supplier;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\Supplier $model */
?>

<div class="supplier-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'source_type',
                'value' => $model->getSourceTypeLabel(),
            ],
            'phone',
            'email:email',
            'address',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (Supplier $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Supplier $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>