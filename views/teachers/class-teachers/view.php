<?php

use app\models\ClassTeacher;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\ClassTeacher $model */
?>

<div class="class-teacher-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'grade_stream_id',
                'value' => $model->getGradeStreamLabel(),
            ],
            [
                'attribute' => 'teacher_id',
                'value' => $model->getTeacherLabel(),
            ],
            [
                'attribute' => 'academic_year_id',
                'value' => $model->getAcademicYearLabel(),
            ],
            'start_date:date',
            'end_date:date',
            [
                'attribute' => 'is_current',
                'value' => $model->getIsCurrentLabel(),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (ClassTeacher $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (ClassTeacher $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>
