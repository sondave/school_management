<?php

use app\models\Student;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
?>

<div class="student-profile-tab">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'upi',
            'nemis_no',
            [
                'label' => 'Full Name',
                'value' => $model->getFullName(),
            ],
            [
                'attribute' => 'gender_id',
                'value' => $model->getGenderLabel(),
            ],
            'date_of_birth:date',
            'birth_cert_no',
            'admission_date:date',
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            [
                'attribute' => 'created_by',
                'value' => static fn(Student $m): string => $m->createdByUser?->username ?? '-',
            ],
            'created_at:datetime',
            [
                'attribute' => 'updated_by',
                'value' => static fn(Student $m): string => $m->updatedByUser?->username ?? '-',
            ],
            'updated_at:datetime',
        ],
    ]) ?>
</div>
