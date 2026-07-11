<?php

use app\models\Parents;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Parents $model */
?>

<div class="parent-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'other_names',
            [
                'attribute' => 'gender',
                'value' => $model->getGenderLabel(),
            ],
            'national_id',
            'date_of_birth:date',
            'phone_no',
            'alternate_phone_no',
            'email:email',
            [
                'attribute' => 'county',
                'value' => $model->getCountyLabel(),
            ],
            'physical_address',
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
                'value' => static function (Parents $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Parents $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
