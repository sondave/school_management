<?php

use app\models\settings\FeesCategory;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Fees Categories';
$this->params['breadcrumbs'][] = ['label' => 'School Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fees-categories-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Fee Category', [
                'class' => 'btn btn-added',
                'id' => 'create-fees-category-button',
                'data-url' => Url::to(['settings/fees-categories/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'fees-category-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'fees-category-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No fee categories found.',
                'tableOptions' => ['class' => 'table datanew no-footer table-hover'],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'name',
                    [
                        'attribute' => 'is_optional',
                        'format' => 'raw',
                        'value' => static function (FeesCategory $model): string {
                            return (int) $model->is_optional === 1
                                ? '<span class="badge bg-info">Yes</span>'
                                : '<span class="badge bg-secondary">No</span>';
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (FeesCategory $model): string {
                            return (int) $model->status === FeesCategory::STATUS_ACTIVE
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        },
                    ],
                    'created_at:datetime',
                    // [
                    //     'attribute' => 'created_by',
                    //     'value' => static fn(FeesCategory $model): string => $model->createdByUser?->username ?? '-',
                    // ],
                    // 'updated_at:datetime',
                    // [
                    //     'attribute' => 'updated_by',
                    //     'value' => static fn(FeesCategory $model): string => $model->updatedByUser?->username ?? '-',
                    // ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, FeesCategory $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item fees-category-view-button',
                                    'data-url' => Url::to(['settings/fees-categories/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item fees-category-update-button',
                                    'data-url' => Url::to(['settings/fees-categories/update', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item fees-category-delete-button',
                                    'data-url' => Url::to(['settings/fees-categories/delete', 'id' => $model->id]),
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

<div class="modal fade" id="fees-category-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fees-category-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="fees-category-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="feesCategoryViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feesCategoryViewModalLabel">Fee Category Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feesCategoryViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openFeesCategoryModal(url, title) {
    var modal = $('#fees-category-modal');
    modal.find('.modal-title').text(title);
    modal.find('#fees-category-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        if (typeof html === 'object' && html.redirectUrl) {
            modal.modal('hide');
            openFeesCategoryModal(html.redirectUrl, 'Update Fee Category');
            return;
        }
        modal.find('#fees-category-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#fees-category-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openFeesCategoryViewModal(url) {
    var modal = $('#feesCategoryViewModal');
    modal.find('#feesCategoryViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#feesCategoryViewModalBody').html(html);
    }).fail(function () {
        modal.find('#feesCategoryViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showFeesCategoryToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#fees-category-message').html(toast);
    var toastEl = document.querySelector('#fees-category-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-fees-category-button', function (e) {
    e.preventDefault();
    openFeesCategoryModal($(this).data('url'), 'Add Fee Category');
});

$(document).on('click', '.fees-category-update-button', function (e) {
    e.preventDefault();
    openFeesCategoryModal($(this).data('url'), 'Update Fee Category');
});

$(document).on('click', '.fees-category-view-button', function (e) {
    e.preventDefault();
    openFeesCategoryViewModal($(this).data('url'));
});

$(document).on('click', '.fees-category-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this fee category';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#fees-category-grid-pjax'});
            showFeesCategoryToast(res.message || 'Fee category deleted successfully.', 'success');
            return;
        }
        showFeesCategoryToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showFeesCategoryToast('Unable to delete fee category.', 'error');
    });
});

$(document).on('beforeSubmit', '#fees-category-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#fees-category-modal').modal('hide');
                $.pjax.reload({container: '#fees-category-grid-pjax'});
                showFeesCategoryToast(res.message || 'Fee category saved successfully.', 'success');
            } else if (res.redirectUrl) {
                $('#fees-category-modal').modal('hide');
                openFeesCategoryModal(res.redirectUrl, 'Update Fee Category');
            } else if (res.html) {
                $('#fees-category-modal-body').html(res.html);
            } else {
                showFeesCategoryToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showFeesCategoryToast('Unable to save fee category.', 'error');
        }
    });

    return false;
});
JS
); ?>
