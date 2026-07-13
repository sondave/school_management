<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Exams';
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exams-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Exam', [
                'class' => 'btn btn-added',
                'id' => 'create-exam-button',
                'data-url' => Url::to(['exams/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'exam-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'exam-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No exams found.',
                'tableOptions' => ['class' => 'table datanew no-footer table-hover'],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],

                    [
                        'attribute' => 'exam_no',
                        'value' => function ($model) {
                            return strtoupper($model->exam_no);
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => static fn($model): string => Html::a(
                            Html::encode((string) $model->name),
                            ['exams/grades', 'id' => $model->id],
                            ['class' => 'text-primary fw-semibold']
                        ),
                    ],
                    [
                        'attribute' => 'academic_year_id',
                        'value' => static fn($model): string => $model->getAcademicYearLabel(),
                    ],
                    [
                        'attribute' => 'term_id',
                        'value' => static fn($model): string => $model->getTermLabel(),
                    ],
                    [
                        'attribute' => 'exam_type_id',
                        'value' => static fn($model): string => $model->getExamTypeLabel(),
                    ],
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function ($model): string {
                            $status = (string) $model->status;
                            if ($status === 'active') {
                                return '<span class="badge bg-success">Active</span>';
                            }
                            if ($status === 'completed') {
                                return '<span class="badge bg-primary">Completed</span>';
                            }
                            return '<span class="badge bg-danger">Canceled</span>';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item exam-view-button',
                                    'data-url' => Url::to(['exams/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="layers" class="info-img"></i> Allocate Grades', Url::to(['exams/grades', 'id' => $model->id]), [
                                    'class' => 'dropdown-item',
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item exam-update-button',
                                    'data-url' => Url::to(['exams/update', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item exam-delete-button',
                                    'data-url' => Url::to(['exams/delete', 'id' => $model->id]),
                                    'data-name' => $model->name,
                                ]) . '</li>';

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

<div class="modal fade" id="exam-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exam-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="exam-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="examViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examViewModalLabel">Exam Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="examViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openExamModal(url, title) {
    var modal = $('#exam-modal');
    modal.find('.modal-title').text(title);
    modal.find('#exam-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#exam-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#exam-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openExamViewModal(url) {
    var modal = $('#examViewModal');
    modal.find('#examViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#examViewModalBody').html(html);
    }).fail(function () {
        modal.find('#examViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showExamToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#exam-message').html(toast);
    var toastEl = document.querySelector('#exam-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-exam-button', function (e) {
    e.preventDefault();
    openExamModal($(this).data('url'), 'Add Exam');
});

$(document).on('click', '.exam-update-button', function (e) {
    e.preventDefault();
    openExamModal($(this).data('url'), 'Update Exam');
});

$(document).on('click', '.exam-view-button', function (e) {
    e.preventDefault();
    openExamViewModal($(this).data('url'));
});

$(document).on('click', '.exam-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this exam';
    var url = $(this).data('url');

    Swal.fire({
        title: 'Delete exam?',
        html: 'You are about to delete <strong>' + name + '</strong>.<br>This will also delete grade assignments linked to this exam.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json'
        }).done(function (res) {
            if (res && res.success) {
                $.pjax.reload({container: '#exam-grid-pjax'});
                showExamToast(res.message || 'Exam deleted successfully.', 'success');
                return;
            }
            showExamToast((res && res.message) || 'Delete failed.', 'error');
        }).fail(function () {
            showExamToast('Unable to delete exam.', 'error');
        });
    });
});

$(document).on('beforeSubmit', '#exam-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#exam-modal').modal('hide');
                $.pjax.reload({container: '#exam-grid-pjax'});
                showExamToast(res.message || 'Exam saved successfully.', 'success');
            } else if (res.html) {
                $('#exam-modal-body').html(res.html);
            } else {
                showExamToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showExamToast('Unable to save exam.', 'error');
        }
    });

    return false;
});
JS
); ?>
