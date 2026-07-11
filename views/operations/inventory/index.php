<?php

use app\models\operations\Inventory;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inventory';
$this->params['breadcrumbs'][] = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Inventory Item', [
                'class' => 'btn btn-added',
                'id' => 'create-inventory-button',
                'data-url' => Url::to(['operations/inventory/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'inventory-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'inventory-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No inventory records found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'accesory_type',
                        'value' => static fn(Inventory $model): string => $model->getAccessoryTypeLabel(),
                    ],
                    [
                        'attribute' => 'inventory_item_id',
                        'value' => static fn(Inventory $model): string => $model->getInventoryItemLabel(),
                    ],
                    [
                        'attribute' => 'supplier_id',
                        'value' => static fn(Inventory $model): string => $model->getSupplierLabel(),
                    ],
                    'quantity',
                    'received_on:date',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Inventory $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item inventory-view-button',
                                    'data-url' => Url::to(['operations/inventory/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item inventory-update-button',
                                    'data-url' => Url::to(['operations/inventory/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item inventory-delete-button',
                                    'data-url' => Url::to(['operations/inventory/delete', 'id' => $model->id]),
                                    'data-name' => $model->getInventoryItemLabel(),
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

<div class="modal fade" id="inventory-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventory-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="inventory-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="inventoryViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryViewModalLabel">Inventory Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="inventoryViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openInventoryModal(url, title) {
    var modal = $('#inventory-modal');
    modal.find('.modal-title').text(title);
    modal.find('#inventory-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventory-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#inventory-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openInventoryViewModal(url) {
    var modal = $('#inventoryViewModal');
    modal.find('#inventoryViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventoryViewModalBody').html(html);
    }).fail(function () {
        modal.find('#inventoryViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showInventoryToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#inventory-message').html(toast);
    var toastEl = document.querySelector('#inventory-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-inventory-button', function (e) {
    e.preventDefault();
    openInventoryModal($(this).data('url'), 'Add Inventory Item');
});

$(document).on('click', '.inventory-update-button', function (e) {
    e.preventDefault();
    openInventoryModal($(this).data('url'), 'Update Inventory Item');
});

$(document).on('click', '.inventory-view-button', function (e) {
    e.preventDefault();
    openInventoryViewModal($(this).data('url'));
});

$(document).on('click', '.inventory-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this inventory item';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#inventory-grid-pjax'});
            showInventoryToast(res.message || 'Inventory item deleted successfully.', 'success');
            return;
        }
        showInventoryToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showInventoryToast('Unable to delete inventory item.', 'error');
    });
});

$(document).on('beforeSubmit', '#inventory-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#inventory-modal').modal('hide');
                $.pjax.reload({container: '#inventory-grid-pjax'});
                showInventoryToast(res.message || 'Inventory item saved successfully.', 'success');
            } else if (res.html) {
                $('#inventory-modal-body').html(res.html);
            } else {
                showInventoryToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showInventoryToast('Unable to save inventory item.', 'error');
        }
    });

    return false;
});
JS
); ?>