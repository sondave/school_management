<?php

use app\models\users\Permission;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Permission';
$this->params['breadcrumbs'][] = 'User Management';
$this->params['breadcrumbs'][] = 'Permissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permissions-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Permission', [
                'class' => 'btn btn-added',
                'id' => 'create-permission-button',
                'data-url' => Url::to(['users/permissions/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'permission-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'permission-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No permissions found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'description',
                        'value' => static function (Permission $model): string {
                            return (string) ($model->description ?: '-');
                        },
                    ],
                    'name',
                    [
                        'attribute' => 'auth_item_group_id',
                        'label' => 'Permission Group',
                        'value' => static function (Permission $model): string {
                            return $model->group?->name ?? '-';
                        },
                    ],
                    
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Permission $model): string {
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', 'javascript:void(0);', [
                                    'class' => 'dropdown-item permission-update-button',
                                    'data-url' => Url::to(['users/permissions/update', 'id' => $model->name]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item permission-delete-button',
                                    'data-url' => Url::to(['users/permissions/delete', 'id' => $model->name]),
                                    'data-name' => $model->name,
                                ]);

                                $items = '<li>' . $update . '</li>';
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
            ]) ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

</div>

<div class="modal fade" id="permission-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permission-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="permission-modal-body"></div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function initPermissionSelect2(modal) {
    var fields = modal.find('select.select2');
    if (!fields.length || typeof $.fn.select2 === 'undefined') {
        return;
    }

    fields.each(function () {
        var field = $(this);
        if (field.data('select2')) {
            field.select2('destroy');
        }

        field.select2({
            width: '100%',
            dropdownParent: modal,
            placeholder: field.find('option:first').text() || 'Select an option',
            allowClear: true
        });
    });
}

function openPermissionModal(url, title) {
    var modal = $('#permission-modal');
    modal.find('.modal-title').text(title);
    modal.find('#permission-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#permission-modal-body').html(html);
        initPermissionSelect2(modal);
    }).fail(function () {
        modal.find('#permission-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function showPermissionToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#permission-message').html(toast);
    var toastEl = document.querySelector('#permission-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-permission-button', function (e) {
    e.preventDefault();
    openPermissionModal($(this).data('url'), 'Add Permission');
});

$(document).on('click', '.permission-update-button', function (e) {
    e.preventDefault();
    openPermissionModal($(this).data('url'), 'Update Permission');
});

$(document).on('click', '.permission-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this permission';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#permission-grid-pjax'});
            showPermissionToast(res.message || 'Permission deleted successfully.', 'success');
            return;
        }
        showPermissionToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showPermissionToast('Unable to delete permission.', 'error');
    });
});

$(document).on('submit', '#permission-form', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $('#permission-modal').modal('hide');
            $.pjax.reload({container: '#permission-grid-pjax'});
            showPermissionToast(res.message || 'Saved successfully.', 'success');
            return;
        }

        if (res && res.html) {
            $('#permission-modal-body').html(res.html);
            initPermissionSelect2($('#permission-modal'));
            return;
        }

        showPermissionToast('Unable to save permission.', 'error');
    }).fail(function () {
        showPermissionToast('Unable to save permission.', 'error');
    });

    return false;
});
JS
); ?>
