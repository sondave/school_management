<?php

use app\models\ClassTeacher;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Class Teachers';
$this->params['breadcrumbs'][] = 'Teachers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-teachers-index">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Class Teacher', [
                'class' => 'btn btn-added',
                'id' => 'create-class-teacher-button',
                'data-url' => Url::to(['teachers/class-teachers/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'class-teacher-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'class-teacher-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No class teachers found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'grade_stream_id',
                        'format' => 'raw',
                        'value' => static function (ClassTeacher $model): string {
                            return Html::a(
                                Html::encode($model->getGradeStreamLabel()),
                                Url::to(['teachers/class-teachers/history', 'gradeStreamId' => $model->grade_stream_id]),
                                ['class' => 'text-primary text-decoration-underline']
                            );
                        },
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
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, ClassTeacher $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item class-teacher-view-button',
                                    'data-url' => Url::to(['teachers/class-teachers/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item class-teacher-update-button',
                                    'data-url' => Url::to(['teachers/class-teachers/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item class-teacher-delete-button',
                                    'data-url' => Url::to(['teachers/class-teachers/delete', 'id' => $model->id]),
                                    'data-name' => $model->getTeacherLabel(),
                                ]);

                                $items = '<li>' . $view . '</li>';
                                $items .= '<li>' . $update . '</li>';
                                $items .= '<li>' . $delete . '</li>';

                                return Html::a('<i class="fa fa-ellipsis-v" aria-hidden="true"></i>', 'javascript:void(0);', [
                                    'class' => 'action-set',
                                    'data-bs-toggle' => 'dropdown',
                                    'aria-expanded' => 'false',
                                ]) . Html::tag('ul', $items, ['class' => 'dropdown-menu']);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="class-teacher-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="class-teacher-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="class-teacher-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="classTeacherViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classTeacherViewModalLabel">Class Teacher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="classTeacherViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openClassTeacherModal(url, title) {
    var modal = $('#class-teacher-modal');
    modal.find('.modal-title').text(title);
    modal.find('#class-teacher-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#class-teacher-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#class-teacher-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openClassTeacherViewModal(url) {
    var modal = $('#classTeacherViewModal');
    modal.find('#classTeacherViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#classTeacherViewModalBody').html(html);
    }).fail(function () {
        modal.find('#classTeacherViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showClassTeacherToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#class-teacher-message').html(toast);
    var toastEl = document.querySelector('#class-teacher-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-class-teacher-button', function (e) {
    e.preventDefault();
    openClassTeacherModal($(this).data('url'), 'Add Class Teacher');
});

$(document).on('click', '.class-teacher-update-button', function (e) {
    e.preventDefault();
    openClassTeacherModal($(this).data('url'), 'Update Class Teacher');
});

$(document).on('click', '.class-teacher-view-button', function (e) {
    e.preventDefault();
    openClassTeacherViewModal($(this).data('url'));
});

$(document).on('click', '.class-teacher-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this class teacher';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#class-teacher-grid-pjax'});
            showClassTeacherToast(res.message || 'Class teacher deleted successfully.', 'success');
            return;
        }
        showClassTeacherToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showClassTeacherToast('Unable to delete class teacher.', 'error');
    });
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
                $('#class-teacher-modal').modal('hide');
                $.pjax.reload({container: '#class-teacher-grid-pjax'});
                showClassTeacherToast(res.message || 'Class teacher saved successfully.', 'success');
            } else if (res.html) {
                $('#class-teacher-modal-body').html(res.html);
            } else {
                showClassTeacherToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showClassTeacherToast('Unable to save class teacher.', 'error');
        }
    });

    return false;
});
JS
); ?>
