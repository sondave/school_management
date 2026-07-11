<?php

use app\models\Parents;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Parents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parents-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Parent', [
                'class' => 'btn btn-added',
                'id' => 'create-parent-button',
                'data-url' => Url::to(['parents/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'parent-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'parent-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No parents found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'first_name',
                    'other_names',
                    [
                        'attribute' => 'gender',
                        'value' => static fn(Parents $model): string => $model->getGenderLabel(),
                    ],
                    'phone_no',
                    [
                        'attribute' => 'county',
                        'value' => static fn(Parents $model): string => $model->getCountyLabel(),
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Parents $model): string {
                            return (int) $model->status === Parents::STATUS_ACTIVE
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Parents $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item parent-view-button',
                                    'data-url' => Url::to(['parents/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item parent-update-button',
                                    'data-url' => Url::to(['parents/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item parent-delete-button',
                                    'data-url' => Url::to(['parents/delete', 'id' => $model->id]),
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

<div class="modal fade" id="parent-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="parent-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="parent-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="parentViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="parentViewModalLabel">Parent Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="parentViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openParentModal(url, title) {
    var modal = $('#parent-modal');
    modal.find('.modal-title').text(title);
    modal.find('#parent-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#parent-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#parent-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openParentViewModal(url) {
    var modal = $('#parentViewModal');
    modal.find('#parentViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#parentViewModalBody').html(html);
    }).fail(function () {
        modal.find('#parentViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showParentToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#parent-message').html(toast);
    var toastEl = document.querySelector('#parent-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-parent-button', function (e) {
    e.preventDefault();
    openParentModal($(this).data('url'), 'Add Parent');
});

$(document).on('click', '.parent-update-button', function (e) {
    e.preventDefault();
    openParentModal($(this).data('url'), 'Update Parent');
});

$(document).on('click', '.parent-view-button', function (e) {
    e.preventDefault();
    openParentViewModal($(this).data('url'));
});

$(document).on('click', '.parent-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this parent';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#parent-grid-pjax'});
            showParentToast(res.message || 'Parent deleted successfully.', 'success');
            return;
        }
        showParentToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showParentToast('Unable to delete parent.', 'error');
    });
});

$(document).on('beforeSubmit', '#parent-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#parent-modal').modal('hide');
                $.pjax.reload({container: '#parent-grid-pjax'});
                showParentToast(res.message || 'Parent saved successfully.', 'success');
            } else if (res.html) {
                $('#parent-modal-body').html(res.html);
            } else {
                showParentToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showParentToast('Unable to save parent.', 'error');
        }
    });

    return false;
});
JS
); ?>
