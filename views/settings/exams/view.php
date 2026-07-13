<?php

use app\models\settings\Exam;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\Exam $model */
?>

<div class="exam-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'exam_no',
            'name',
            [
                'attribute' => 'academic_year_id',
                'value' => $model->getAcademicYearLabel(),
            ],
            [
                'attribute' => 'term_id',
                'value' => $model->getTermLabel(),
            ],
            [
                'attribute' => 'exam_type_id',
                'value' => $model->getExamTypeLabel(),
            ],
            'start_date:date',
            'end_date:date',
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
                'value' => static function (Exam $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Exam $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
