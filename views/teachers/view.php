<?php

use app\models\Teacher;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */
?>

<div class="teacher-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'other_names',
            'phone_number',
            'alternate_phone_number',
            'email_address:email',
            'date_of_birth:date',
            [
                'attribute' => 'employment_type',
                'value' => $model->getEmploymentTypeLabel(),
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            'tsc_number',
            'staff_number',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (Teacher $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Teacher $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
