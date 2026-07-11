<?php

use app\models\operations\InventoryDispersal;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryDispersal $model */
?>

<div class="inventory-dispersal-view">
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
                'attribute' => 'dispersed_to',
                'value' => $model->getDispersedToLabel(),
            ],
            [
                'attribute' => 'teacher_id',
                'value' => $model->getTeacherLabel(),
            ],
            [
                'attribute' => 'grade_id',
                'value' => $model->getGradeLabel(),
            ],
            [
                'attribute' => 'student_id',
                'value' => $model->getStudentLabel(),
            ],
            [
                'attribute' => 'academic_year_id',
                'value' => $model->getAcademicYearLabel(),
            ],
            [
                'attribute' => 'term_id',
                'value' => $model->getTermLabel(),
            ],
            'dispersed_on:date',
            'qty_dispersed',
            [
                'attribute' => 'is_to_be_returned',
                'value' => (int) $model->is_to_be_returned === 1 ? 'Yes' : 'No',
            ],
            'returned_on:date',
            'qty_returned',
            'missplaced',
            'remarks:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (InventoryDispersal $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (InventoryDispersal $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>
</div>