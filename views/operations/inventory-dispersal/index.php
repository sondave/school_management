<?php

use app\models\operations\InventoryDispersal;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inventory Dispersal';
$this->params['breadcrumbs'][] = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-dispersal-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Inventory Dispersal', [
                'class' => 'btn btn-added',
                'id' => 'create-inventory-dispersal-button',
                'data-url' => Url::to(['operations/inventory-dispersal/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'inventory-dispersal-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'inventory-dispersal-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No inventory dispersal records found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'accesory_type',
                        'value' => static fn(InventoryDispersal $model): string => $model->getAccessoryTypeLabel(),
                    ],
                    [
                        'attribute' => 'inventory_item_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getInventoryItemLabel(),
                    ],
                    [
                        'attribute' => 'dispersed_to',
                        'value' => static fn(InventoryDispersal $model): string => $model->getDispersedToLabel(),
                    ],
                    [
                        'attribute' => 'teacher_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getTeacherLabel(),
                    ],
                    [
                        'attribute' => 'grade_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getGradeLabel(),
                    ],
                    [
                        'attribute' => 'student_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getStudentLabel(),
                    ],
                    [
                        'attribute' => 'academic_year_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getAcademicYearLabel(),
                    ],
                    [
                        'attribute' => 'term_id',
                        'value' => static fn(InventoryDispersal $model): string => $model->getTermLabel(),
                    ],
                    'dispersed_on:date',
                    'qty_dispersed',
                    [
                        'attribute' => 'is_to_be_returned',
                        'value' => static fn(InventoryDispersal $model): string => (int) $model->is_to_be_returned === 1 ? 'Yes' : 'No',
                    ],
                    'returned_on:date',
                    'qty_returned',
                    'missplaced',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, InventoryDispersal $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item inventory-dispersal-view-button',
                                    'data-url' => Url::to(['operations/inventory-dispersal/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item inventory-dispersal-update-button',
                                    'data-url' => Url::to(['operations/inventory-dispersal/update', 'id' => $model->id]),
                                ]);
                                $receiveBack = Html::a('<i data-feather="corner-up-left" class="info-img"></i> Receive Back', '#', [
                                    'class' => 'dropdown-item inventory-dispersal-receive-back-button',
                                    'data-url' => Url::to(['operations/inventory-dispersal/receive-back', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item inventory-dispersal-delete-button',
                                    'data-url' => Url::to(['operations/inventory-dispersal/delete', 'id' => $model->id]),
                                    'data-name' => $model->getInventoryItemLabel(),
                                ]);

                                $items = '<li>' . $view . '</li>';
                                $items .= '<li>' . $update . '</li>';
                                $items .= '<li>' . $receiveBack . '</li>';
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

<div class="modal fade" id="inventory-dispersal-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventory-dispersal-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="inventory-dispersal-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="inventory-dispersal-view-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventory-dispersal-view-modal-label">Inventory Dispersal Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="inventory-dispersal-view-modal-body">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openInventoryDispersalModal(url, title) {
    var modal = $('#inventory-dispersal-modal');
    modal.find('.modal-title').text(title);
    modal.find('#inventory-dispersal-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventory-dispersal-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#inventory-dispersal-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openInventoryDispersalViewModal(url) {
    var modal = $('#inventory-dispersal-view-modal');
    modal.find('#inventory-dispersal-view-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#inventory-dispersal-view-modal-body').html(html);
    }).fail(function () {
        modal.find('#inventory-dispersal-view-modal-body').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showInventoryDispersalToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#inventory-dispersal-message').html(toast);
    var toastEl = document.querySelector('#inventory-dispersal-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-inventory-dispersal-button', function (e) {
    e.preventDefault();
    openInventoryDispersalModal($(this).data('url'), 'Add Inventory Dispersal');
});

$(document).on('click', '.inventory-dispersal-update-button', function (e) {
    e.preventDefault();
    openInventoryDispersalModal($(this).data('url'), 'Update Inventory Dispersal');
});

$(document).on('click', '.inventory-dispersal-receive-back-button', function (e) {
    e.preventDefault();
    openInventoryDispersalModal($(this).data('url'), 'Receive Back');
});

$(document).on('click', '.inventory-dispersal-view-button', function (e) {
    e.preventDefault();
    openInventoryDispersalViewModal($(this).data('url'));
});

$(document).on('click', '.inventory-dispersal-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this dispersal record';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#inventory-dispersal-grid-pjax'});
            showInventoryDispersalToast(res.message || 'Inventory dispersal deleted successfully.', 'success');
            return;
        }
        showInventoryDispersalToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showInventoryDispersalToast('Unable to delete inventory dispersal.', 'error');
    });
});

$(document).on('beforeSubmit', '#inventory-dispersal-form, #inventory-dispersal-receive-back-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#inventory-dispersal-modal').modal('hide');
                $.pjax.reload({container: '#inventory-dispersal-grid-pjax'});
                showInventoryDispersalToast(res.message || 'Inventory dispersal saved successfully.', 'success');
            } else if (res.html) {
                $('#inventory-dispersal-modal-body').html(res.html);
            } else {
                showInventoryDispersalToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showInventoryDispersalToast('Unable to save inventory dispersal.', 'error');
        }
    });

    return false;
});
JS
); ?>