<?php

use app\models\users\Role;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Roles';
$this->params['breadcrumbs'][] = 'User Management';
$this->params['breadcrumbs'][] = 'Permissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Role', [
                'class' => 'btn btn-added',
                'id' => 'create-role-button',
                'data-url' => Url::to(['users/roles/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'role-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'role-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No roles found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'name',
                    [
                        'attribute' => 'description',
                        'value' => static function (Role $model): string {
                            return (string) ($model->description ?: '-');
                        },
                    ],
                    [
                        'label' => 'Permissions',
                        'format' => 'raw',
                        'value' => static function (Role $model): string {
                            if ($model->permissionNames === []) {
                                return '<span class="badge bg-secondary">None</span>';
                            }

                            return '<span class="badge bg-info">' . count($model->permissionNames) . ' assigned</span>';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Role $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', 'javascript:void(0);', [
                                    'class' => 'dropdown-item role-view-button',
                                    'data-url' => Url::to(['users/roles/view', 'id' => $model->name]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', 'javascript:void(0);', [
                                    'class' => 'dropdown-item role-update-button',
                                    'data-url' => Url::to(['users/roles/update', 'id' => $model->name]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item role-delete-button',
                                    'data-url' => Url::to(['users/roles/delete', 'id' => $model->name]),
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
            ]) ?>

            <?php Pjax::end(); ?>
        </div>
    </div>

</div>

<div class="modal fade" id="role-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="role-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="role-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="role-view-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="role-view-modal-label">Role Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="role-view-modal-body">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function initRoleSelect2(modal) {
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
            placeholder: field.data('placeholder') || 'Select option',
            allowClear: field.prop('multiple') !== true,
            closeOnSelect: field.prop('multiple') !== true
        });
    });
}

function openRoleModal(url, title) {
    var modal = $('#role-modal');
    modal.find('.modal-title').text(title);
    modal.find('#role-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#role-modal-body').html(html);
        initRoleSelect2(modal);
    }).fail(function () {
        modal.find('#role-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openRoleViewModal(url) {
    var modal = $('#role-view-modal');
    modal.find('#role-view-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#role-view-modal-body').html(html);
    }).fail(function () {
        modal.find('#role-view-modal-body').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showRoleToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#role-message').html(toast);
    var toastEl = document.querySelector('#role-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-role-button', function (e) {
    e.preventDefault();
    openRoleModal($(this).data('url'), 'Add Role');
});

$(document).on('click', '.role-update-button', function (e) {
    e.preventDefault();
    openRoleModal($(this).data('url'), 'Update Role');
});

$(document).on('click', '.role-view-button', function (e) {
    e.preventDefault();
    openRoleViewModal($(this).data('url'));
});

$(document).on('click', '.role-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this role';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#role-grid-pjax'});
            showRoleToast(res.message || 'Role deleted successfully.', 'success');
            return;
        }
        showRoleToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showRoleToast('Unable to delete role.', 'error');
    });
});

$(document).on('submit', '#role-form', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $('#role-modal').modal('hide');
            $.pjax.reload({container: '#role-grid-pjax'});
            showRoleToast(res.message || 'Saved successfully.', 'success');
            return;
        }

        if (res && res.html) {
            $('#role-modal-body').html(res.html);
            initRoleSelect2($('#role-modal'));
            return;
        }

        showRoleToast('Unable to save role.', 'error');
    }).fail(function () {
        showRoleToast('Unable to save role.', 'error');
    });

    return false;
});
JS
); ?>

