<?php

use app\models\settings\FeesStructure;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Fees Structure';
$this->params['breadcrumbs'][] = ['label' => 'School Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fees-structure-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Fee Structure', [
                'class' => 'btn btn-added',
                'id' => 'create-fees-structure-button',
                'data-url' => Url::to(['settings/fees-structures/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'fees-structure-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'fees-structure-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No fee structures found.',
                'tableOptions' => ['class' => 'table datanew no-footer table-hover'],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'academic_year_id',
                        'value' => static fn(FeesStructure $model): string => $model->academicYear?->year ?? '-',
                    ],
                    [
                        'attribute' => 'term_id',
                        'value' => static fn(FeesStructure $model): string => $model->term?->name ?? '-',
                    ],
                    [
                        'attribute' => 'grade_id',
                        'value' => static fn(FeesStructure $model): string => $model->grade?->grade ?? '-',
                    ],
                    [
                        'attribute' => 'category_id',
                        'value' => static fn(FeesStructure $model): string => $model->category?->name ?? '-',
                    ],
                    [
                        'attribute' => 'amount',
                        'format' => ['decimal', 2],
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (FeesStructure $model): string {
                            return (int) $model->status === FeesStructure::STATUS_ACTIVE
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        },
                    ],
                    'created_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => static fn(FeesStructure $model): string => $model->createdByUser?->username ?? '-',
                    ],
                    // 'updated_at:datetime',
                    // [
                    //     'attribute' => 'updated_by',
                    //     'value' => static fn(FeesStructure $model): string => $model->updatedByUser?->username ?? '-',
                    // ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, FeesStructure $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item fees-structure-view-button',
                                    'data-url' => Url::to(['settings/fees-structures/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item fees-structure-update-button',
                                    'data-url' => Url::to(['settings/fees-structures/update', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item fees-structure-delete-button',
                                    'data-url' => Url::to(['settings/fees-structures/delete', 'id' => $model->id]),
                                    'data-name' => $model->academicYear?->year . ' ' . $model->term?->name . ' ' . $model->grade?->grade,
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

<div class="modal fade" id="fees-structure-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fees-structure-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="fees-structure-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="feesStructureViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feesStructureViewModalLabel">Fee Structure Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feesStructureViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openFeesStructureModal(url, title) {
    var modal = $('#fees-structure-modal');
    modal.find('.modal-title').text(title);
    modal.find('#fees-structure-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        if (typeof html === 'object' && html.redirectUrl) {
            modal.modal('hide');
            openFeesStructureModal(html.redirectUrl, 'Update Fee Structure');
            return;
        }
        modal.find('#fees-structure-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#fees-structure-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openFeesStructureViewModal(url) {
    var modal = $('#feesStructureViewModal');
    modal.find('#feesStructureViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#feesStructureViewModalBody').html(html);
    }).fail(function () {
        modal.find('#feesStructureViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showFeesStructureToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#fees-structure-message').html(toast);
    var toastEl = document.querySelector('#fees-structure-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-fees-structure-button', function (e) {
    e.preventDefault();
    openFeesStructureModal($(this).data('url'), 'Add Fee Structure');
});

$(document).on('click', '.fees-structure-update-button', function (e) {
    e.preventDefault();
    openFeesStructureModal($(this).data('url'), 'Update Fee Structure');
});

$(document).on('click', '.fees-structure-view-button', function (e) {
    e.preventDefault();
    openFeesStructureViewModal($(this).data('url'));
});

$(document).on('click', '.fees-structure-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this fee structure';
    var url = $(this).data('url');

    Swal.fire({
        title: 'Delete fee structure?',
        html: 'You are about to delete <strong>' + name + '</strong>.<br>This will also delete all linked fee allocations for students.',
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
                $.pjax.reload({container: '#fees-structure-grid-pjax'});
                showFeesStructureToast(res.message || 'Fee structure deleted successfully.', 'success');
                return;
            }
            showFeesStructureToast((res && res.message) || 'Delete failed.', 'error');
        }).fail(function () {
            showFeesStructureToast('Unable to delete fee structure.', 'error');
        });
    });
});

$(document).on('beforeSubmit', '#fees-structure-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#fees-structure-modal').modal('hide');
                $.pjax.reload({container: '#fees-structure-grid-pjax'});
                showFeesStructureToast(res.message || 'Fee structure saved successfully.', 'success');
            } else if (res.redirectUrl) {
                $('#fees-structure-modal').modal('hide');
                openFeesStructureModal(res.redirectUrl, 'Update Fee Structure');
            } else if (res.html) {
                $('#fees-structure-modal-body').html(res.html);
            } else {
                showFeesStructureToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showFeesStructureToast('Unable to save fee structure.', 'error');
        }
    });

    return false;
});
JS
); ?>
