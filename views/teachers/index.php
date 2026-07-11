<?php

use app\models\Teacher;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Teachers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teachers-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Teacher', [
                'class' => 'btn btn-added',
                'id' => 'create-teacher-button',
                'data-url' => Url::to(['teachers/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'teacher-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'teacher-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No teachers found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'first_name',
                    'other_names',
                    'phone_number',
                    'email_address:email',
                    [
                        'attribute' => 'employment_type',
                        'value' => static fn(Teacher $model): string => $model->getEmploymentTypeLabel(),
                    ],
                    [
                        'attribute' => 'status',
                        'value' => static fn(Teacher $model): string => $model->getStatusLabel(),
                    ],
                    'tsc_number',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Teacher $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item teacher-view-button',
                                    'data-url' => Url::to(['teachers/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item teacher-update-button',
                                    'data-url' => Url::to(['teachers/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item teacher-delete-button',
                                    'data-url' => Url::to(['teachers/delete', 'id' => $model->id]),
                                    'data-name' => $model->first_name . ' ' . $model->other_names,
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

<div class="modal fade" id="teacher-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teacher-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="teacher-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="teacherViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teacherViewModalLabel">Teacher Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="teacherViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openTeacherModal(url, title) {
    var modal = $('#teacher-modal');
    modal.find('.modal-title').text(title);
    modal.find('#teacher-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#teacher-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#teacher-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openTeacherViewModal(url) {
    var modal = $('#teacherViewModal');
    modal.find('#teacherViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#teacherViewModalBody').html(html);
    }).fail(function () {
        modal.find('#teacherViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showTeacherToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#teacher-message').html(toast);
    var toastEl = document.querySelector('#teacher-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-teacher-button', function (e) {
    e.preventDefault();
    openTeacherModal($(this).data('url'), 'Add Teacher');
});

$(document).on('click', '.teacher-update-button', function (e) {
    e.preventDefault();
    openTeacherModal($(this).data('url'), 'Update Teacher');
});

$(document).on('click', '.teacher-view-button', function (e) {
    e.preventDefault();
    openTeacherViewModal($(this).data('url'));
});

$(document).on('click', '.teacher-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this teacher';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#teacher-grid-pjax'});
            showTeacherToast(res.message || 'Teacher deleted successfully.', 'success');
            return;
        }
        showTeacherToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showTeacherToast('Unable to delete teacher.', 'error');
    });
});

$(document).on('beforeSubmit', '#teacher-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#teacher-modal').modal('hide');
                $.pjax.reload({container: '#teacher-grid-pjax'});
                showTeacherToast(res.message || 'Teacher saved successfully.', 'success');
            } else if (res.html) {
                $('#teacher-modal-body').html(res.html);
            } else {
                showTeacherToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showTeacherToast('Unable to save teacher.', 'error');
        }
    });

    return false;
});
JS
); ?>
