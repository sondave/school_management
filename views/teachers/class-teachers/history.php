<?php

use app\models\ClassTeacher;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $gradeStreamId */
/** @var string $gradeStreamLabel */

$this->title = 'Class Teacher History';
$this->params['breadcrumbs'][] = 'Teachers';
$this->params['breadcrumbs'][] = ['label' => 'Class Teachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-teacher-history">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['teachers/class-teachers/index']) ?>">Class Teachers</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($gradeStreamLabel) ?></li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::a('<i data-feather="arrow-left" class="me-2"></i> Back to Class Teachers', ['teachers/class-teachers/index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="mb-3 fw-semibold">
                Grade Stream: <?= Html::encode($gradeStreamLabel) ?>
            </div>

            <?php Pjax::begin(['id' => 'class-teacher-history-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No class teacher history found for this grade stream.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'teacher_id',
                        'value' => static fn(ClassTeacher $model): string => $model->getTeacherLabel(),
                    ],
                    [
                        'attribute' => 'academic_year_id',
                        'value' => static fn(ClassTeacher $model): string => $model->getAcademicYearLabel(),
                    ],
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'is_current',
                        'format' => 'raw',
                        'value' => static function (ClassTeacher $model): string {
                            return (int) $model->is_current === ClassTeacher::CURRENT_YES
                                ? '<span class="badge bg-success">Current</span>'
                                : '<span class="badge bg-secondary">Not Current</span>';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {update}',
                        'buttons' => [
                            'view' => static function ($url, ClassTeacher $model): string {
                                return Html::a('<i class="fa fa-eye"></i>', ['teachers/class-teachers/view', 'id' => $model->id], [
                                    'title' => 'View',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-sm btn-outline-info me-1 class-teacher-view-button',
                                    'data-url' => Url::to(['teachers/class-teachers/view', 'id' => $model->id]),
                                ]);
                            },
                            'update' => static function ($url, ClassTeacher $model): string {
                                return Html::a('<i class="fa fa-edit"></i>', ['teachers/class-teachers/update', 'id' => $model->id], [
                                    'title' => 'Update',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-sm btn-outline-primary class-teacher-update-button',
                                    'data-url' => Url::to(['teachers/class-teachers/update', 'id' => $model->id]),
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
