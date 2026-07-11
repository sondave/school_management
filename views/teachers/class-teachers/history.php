<?php

use app\models\ClassTeacher;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var int $gradeId */
/** @var string $gradeLabel */

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
                    <li class="breadcrumb-item active"><?= Html::encode($gradeLabel) ?></li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::a('<i data-feather="arrow-left" class="me-2"></i> Back to Class Teachers', ['teachers/class-teachers/index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'class-teacher-history-message']) ?>

    <div class="card">
        <div class="card-body">
            <div class="mb-3 fw-semibold">
                Grade: <?= Html::encode($gradeLabel) ?>
            </div>

            <?php Pjax::begin(['id' => 'class-teacher-history-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No class teacher history found for this grade.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'grade_id',
                        'value' => static fn(ClassTeacher $model): string => $model->getGradeLabel(),
                    ],
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
                                return Html::a('<i class="fa fa-eye"></i>', '#', [
                                    'title' => 'View',
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-sm btn-outline-info me-1 class-teacher-view-button',
                                    'data-url' => Url::to(['teachers/class-teachers/view', 'id' => $model->id]),
                                ]);
                            },
                            'update' => static function ($url, ClassTeacher $model): string {
                                return Html::a('<i class="fa fa-edit"></i>', '#', [
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

<div class="modal fade" id="class-teacher-history-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="class-teacher-history-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="class-teacher-history-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="classTeacherHistoryViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classTeacherHistoryViewModalLabel">Class Teacher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="classTeacherHistoryViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openClassTeacherHistoryModal(url, title) {
    var modal = $('#class-teacher-history-modal');
    modal.find('.modal-title').text(title);
    modal.find('#class-teacher-history-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#class-teacher-history-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#class-teacher-history-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openClassTeacherHistoryViewModal(url) {
    var modal = $('#classTeacherHistoryViewModal');
    modal.find('#classTeacherHistoryViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#classTeacherHistoryViewModalBody').html(html);
    }).fail(function () {
        modal.find('#classTeacherHistoryViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showClassTeacherHistoryToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#class-teacher-history-message').html(toast);
    var toastEl = document.querySelector('#class-teacher-history-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '.class-teacher-update-button', function (e) {
    e.preventDefault();
    openClassTeacherHistoryModal($(this).data('url'), 'Update Class Teacher');
});

$(document).on('click', '.class-teacher-view-button', function (e) {
    e.preventDefault();
    openClassTeacherHistoryViewModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#class-teacher-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#class-teacher-history-modal').modal('hide');
                $.pjax.reload({container: '#class-teacher-history-grid-pjax'});
                showClassTeacherHistoryToast(res.message || 'Class teacher updated successfully.', 'success');
            } else if (res.html) {
                $('#class-teacher-history-modal-body').html(res.html);
            } else {
                showClassTeacherHistoryToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showClassTeacherHistoryToast('Unable to save class teacher.', 'error');
        }
    });

    return false;
});
JS
); ?>
