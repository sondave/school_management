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
            'admission_type',
            'transfered_from',
            
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
                'attribute' => 'has_special_needs',
                'value' => $model->getHasSpecialNeedsLabel(),
            ],
            'upi',
            'access_number',
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            'transfered_to',
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
