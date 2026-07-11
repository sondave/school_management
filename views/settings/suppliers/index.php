<?php

use app\models\settings\Supplier;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Suppliers';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="suppliers-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Supplier', [
                'class' => 'btn btn-added',
                'id' => 'create-supplier-button',
                'data-url' => Url::to(['settings/suppliers/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'supplier-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'supplier-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No suppliers found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'name',
                    [
                        'attribute' => 'source_type',
                        'value' => static fn(Supplier $model): string => $model->getSourceTypeLabel(),
                    ],
                    'phone',
                    'email:email',
                    'address',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Supplier $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item supplier-view-button',
                                    'data-url' => Url::to(['settings/suppliers/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item supplier-update-button',
                                    'data-url' => Url::to(['settings/suppliers/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item supplier-delete-button',
                                    'data-url' => Url::to(['settings/suppliers/delete', 'id' => $model->id]),
                                    'data-name' => $model->name,
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

<div class="modal fade" id="supplier-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplier-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="supplier-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="supplierViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierViewModalLabel">Supplier Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="supplierViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openSupplierModal(url, title) {
    var modal = $('#supplier-modal');
    modal.find('.modal-title').text(title);
    modal.find('#supplier-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#supplier-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#supplier-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openSupplierViewModal(url) {
    var modal = $('#supplierViewModal');
    modal.find('#supplierViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#supplierViewModalBody').html(html);
    }).fail(function () {
        modal.find('#supplierViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showSupplierToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#supplier-message').html(toast);
    var toastEl = document.querySelector('#supplier-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-supplier-button', function (e) {
    e.preventDefault();
    openSupplierModal($(this).data('url'), 'Add Supplier');
});

$(document).on('click', '.supplier-update-button', function (e) {
    e.preventDefault();
    openSupplierModal($(this).data('url'), 'Update Supplier');
});

$(document).on('click', '.supplier-view-button', function (e) {
    e.preventDefault();
    openSupplierViewModal($(this).data('url'));
});

$(document).on('click', '.supplier-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this supplier';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#supplier-grid-pjax'});
            showSupplierToast(res.message || 'Supplier deleted successfully.', 'success');
            return;
        }
        showSupplierToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showSupplierToast('Unable to delete supplier.', 'error');
    });
});

$(document).on('beforeSubmit', '#supplier-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#supplier-modal').modal('hide');
                $.pjax.reload({container: '#supplier-grid-pjax'});
                showSupplierToast(res.message || 'Supplier saved successfully.', 'success');
            } else if (res.html) {
                $('#supplier-modal-body').html(res.html);
            } else {
                showSupplierToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showSupplierToast('Unable to save supplier.', 'error');
        }
    });

    return false;
});
JS
); ?>