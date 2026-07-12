<?php

use app\models\settings\FeesCategory;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\FeesCategory $model */
?>

<div class="fees-category-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'is_optional',
                'value' => $model->getIsOptionalLabel(),
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
                'value' => static function (FeesCategory $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (FeesCategory $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
