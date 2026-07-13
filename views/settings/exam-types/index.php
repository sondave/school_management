<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Exam Types';
$this->params['breadcrumbs'][] = ['label' => 'Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exam-types-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Exam Type', [
                'class' => 'btn btn-added',
                'id' => 'create-exam-type-button',
                'data-url' => Url::to(['settings/exam-types/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'exam-type-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'exam-type-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No exam types found.',
                'tableOptions' => ['class' => 'table datanew no-footer table-hover'],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'code',
                    'name',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function ($model): string {
                            return (int) $model->status === 1
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        },
                    ],
                    'created_at:datetime',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item exam-type-view-button',
                                    'data-url' => Url::to(['settings/exam-types/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item exam-type-update-button',
                                    'data-url' => Url::to(['settings/exam-types/update', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item exam-type-delete-button',
                                    'data-url' => Url::to(['settings/exam-types/delete', 'id' => $model->id]),
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

<div class="modal fade" id="exam-type-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exam-type-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="exam-type-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="examTypeViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="examTypeViewModalLabel">Exam Type Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="examTypeViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openExamTypeModal(url, title) {
    var modal = $('#exam-type-modal');
    modal.find('.modal-title').text(title);
    modal.find('#exam-type-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#exam-type-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#exam-type-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openExamTypeViewModal(url) {
    var modal = $('#examTypeViewModal');
    modal.find('#examTypeViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#examTypeViewModalBody').html(html);
    }).fail(function () {
        modal.find('#examTypeViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showExamTypeToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#exam-type-message').html(toast);
    var toastEl = document.querySelector('#exam-type-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-exam-type-button', function (e) {
    e.preventDefault();
    openExamTypeModal($(this).data('url'), 'Add Exam Type');
});

$(document).on('click', '.exam-type-update-button', function (e) {
    e.preventDefault();
    openExamTypeModal($(this).data('url'), 'Update Exam Type');
});

$(document).on('click', '.exam-type-view-button', function (e) {
    e.preventDefault();
    openExamTypeViewModal($(this).data('url'));
});

$(document).on('click', '.exam-type-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this exam type';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#exam-type-grid-pjax'});
            showExamTypeToast(res.message || 'Exam type deleted successfully.', 'success');
            return;
        }
        showExamTypeToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showExamTypeToast('Unable to delete exam type.', 'error');
    });
});

$(document).on('beforeSubmit', '#exam-type-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#exam-type-modal').modal('hide');
                $.pjax.reload({container: '#exam-type-grid-pjax'});
                showExamTypeToast(res.message || 'Exam type saved successfully.', 'success');
            } else if (res.html) {
                $('#exam-type-modal-body').html(res.html);
            } else {
                showExamTypeToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showExamTypeToast('Unable to save exam type.', 'error');
        }
    });

    return false;
});
JS
); ?>
