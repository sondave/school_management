<?php

use app\models\operations\InventoryItem;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inventory Items';
$this->params['breadcrumbs'][] = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-items-index">

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
                'id' => 'create-inventory-item-button',
                'data-url' => Url::to(['operations/inventory-items/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'inventory-item-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'inventory-item-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No inventory items found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'accesory_type',
                        'value' => static fn(InventoryItem $model): string => $model->getAccessoryTypeLabel(),
                    ],
                    'name',
                    [
                        'attribute' => 'description',
                        'value' => static fn(InventoryItem $model): string => trim((string) $model->description) ?: '-',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, InventoryItem $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item inventory-item-view-button',
                                    'data-url' => Url::to(['operations/inventory-items/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item inventory-item-update-button',
                                    'data-url' => Url::to(['operations/inventory-items/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item inventory-item-delete-button',
                                    'data-url' => Url::to(['operations/inventory-items/delete', 'id' => $model->id]),
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

<div class="modal fade" id="inventory-item-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventory-item-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="inventory-item-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="inventoryItemViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryItemViewModalLabel">Inventory Item Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="inventoryItemViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openInventoryItemModal(url, title) {
    var modal = $('#inventory-item-modal');
    modal.find('.modal-title').text(title);
    modal.find('#inventory-item-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventory-item-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#inventory-item-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openInventoryItemViewModal(url) {
    var modal = $('#inventoryItemViewModal');
    modal.find('#inventoryItemViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventoryItemViewModalBody').html(html);
    }).fail(function () {
        modal.find('#inventoryItemViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showInventoryItemToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#inventory-item-message').html(toast);
    var toastEl = document.querySelector('#inventory-item-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-inventory-item-button', function (e) {
    e.preventDefault();
    openInventoryItemModal($(this).data('url'), 'Add Inventory Item');
});

$(document).on('click', '.inventory-item-update-button', function (e) {
    e.preventDefault();
    openInventoryItemModal($(this).data('url'), 'Update Inventory Item');
});

$(document).on('click', '.inventory-item-view-button', function (e) {
    e.preventDefault();
    openInventoryItemViewModal($(this).data('url'));
});

$(document).on('click', '.inventory-item-delete-button', function (e) {
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
            $.pjax.reload({container: '#inventory-item-grid-pjax'});
            showInventoryItemToast(res.message || 'Inventory item deleted successfully.', 'success');
            return;
        }
        showInventoryItemToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showInventoryItemToast('Unable to delete inventory item.', 'error');
    });
});

$(document).on('beforeSubmit', '#inventory-item-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#inventory-item-modal').modal('hide');
                $.pjax.reload({container: '#inventory-item-grid-pjax'});
                showInventoryItemToast(res.message || 'Inventory item saved successfully.', 'success');
            } else if (res.html) {
                $('#inventory-item-modal-body').html(res.html);
            } else {
                showInventoryItemToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showInventoryItemToast('Unable to save inventory item.', 'error');
        }
    });

    return false;
});
JS
); ?>