<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Teacher Subjects';
$this->params['breadcrumbs'][] = 'Teachers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-subjects-index">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['teachers/index']) ?>">Teachers</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Teacher Subjects', [
                'class' => 'btn btn-added',
                'id' => 'create-teacher-subject-button',
                'data-url' => Url::to(['teachers/teacher-subjects/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'teacher-subject-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'teacher-subject-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No teacher subjects found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'label' => 'Teacher',
                        'value' => static fn(array $row): string => $row['teacher_label'],
                    ],
                    [
                        'label' => 'Grade',
                        'value' => static fn(array $row): string => $row['grade_label'],
                    ],
                    [
                        'label' => 'Academic Year',
                        'value' => static fn(array $row): string => $row['academic_year_label'],
                    ],
                    [
                        'label' => 'Subjects',
                        'value' => static fn(array $row): string => $row['subjects_label'],
                    ],
                    'start_date:date',
                    'end_date:date',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, array $row): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item teacher-subject-view-button',
                                    'data-url' => Url::to(['teachers/teacher-subjects/view', 'id' => $row['id']]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item teacher-subject-update-button',
                                    'data-url' => Url::to(['teachers/teacher-subjects/update', 'id' => $row['id']]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item teacher-subject-delete-button',
                                    'data-url' => Url::to(['teachers/teacher-subjects/delete', 'id' => $row['id']]),
                                    'data-name' => $row['teacher_label'] . ' / ' . $row['grade_label'],
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

<div class="modal fade" id="teacher-subject-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teacher-subject-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="teacher-subject-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="teacherSubjectViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teacherSubjectViewModalLabel">Teacher Subject Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="teacherSubjectViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openTeacherSubjectModal(url, title) {
    var modal = $('#teacher-subject-modal');
    modal.find('.modal-title').text(title);
    modal.find('#teacher-subject-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#teacher-subject-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#teacher-subject-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openTeacherSubjectViewModal(url) {
    var modal = $('#teacherSubjectViewModal');
    modal.find('#teacherSubjectViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#teacherSubjectViewModalBody').html(html);
    }).fail(function () {
        modal.find('#teacherSubjectViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showTeacherSubjectToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#teacher-subject-message').html(toast);
    var toastEl = document.querySelector('#teacher-subject-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-teacher-subject-button', function (e) {
    e.preventDefault();
    openTeacherSubjectModal($(this).data('url'), 'Add Teacher Subjects');
});

$(document).on('click', '.teacher-subject-update-button', function (e) {
    e.preventDefault();
    openTeacherSubjectModal($(this).data('url'), 'Update Teacher Subjects');
});

$(document).on('click', '.teacher-subject-view-button', function (e) {
    e.preventDefault();
    openTeacherSubjectViewModal($(this).data('url'));
});

$(document).on('click', '.teacher-subject-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this teacher subject assignment';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#teacher-subject-grid-pjax'});
            showTeacherSubjectToast(res.message || 'Teacher subjects deleted successfully.', 'success');
            return;
        }
        showTeacherSubjectToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showTeacherSubjectToast('Unable to delete teacher subjects.', 'error');
    });
});

$(document).on('beforeSubmit', '#teacher-subject-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#teacher-subject-modal').modal('hide');
                $.pjax.reload({container: '#teacher-subject-grid-pjax'});
                showTeacherSubjectToast(res.message || 'Teacher subjects saved successfully.', 'success');
            } else if (res.html) {
                $('#teacher-subject-modal-body').html(res.html);
            } else {
                showTeacherSubjectToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showTeacherSubjectToast('Unable to save teacher subjects.', 'error');
        }
    });

    return false;
});
JS
); ?>