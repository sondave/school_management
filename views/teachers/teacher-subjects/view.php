<?php

use app\models\TeacherSubject;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TeacherSubject $model */
?>

<div class="teacher-subject-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'teacher_id',
                'value' => $model->getTeacherLabel(),
            ],
            [
                'attribute' => 'grade_id',
                'value' => $model->getGradeLabel(),
            ],
            [
                'attribute' => 'academic_year_id',
                'value' => $model->getAcademicYearLabel(),
            ],
            [
                'attribute' => 'subject_ids',
                'value' => implode(', ', $model->getSelectedSubjectLabels()),
            ],
            'start_date:date',
            'end_date:date',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (TeacherSubject $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (TeacherSubject $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>