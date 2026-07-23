<?php

use app\models\users\PermissionGroup;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Permission Groups';
$this->params['breadcrumbs'][] = 'User Management';
$this->params['breadcrumbs'][] = 'Permissions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-groups-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Permission Group', [
                'class' => 'btn btn-added',
                'id' => 'create-permission-group-button',
                'data-url' => Url::to(['users/permission-groups/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'permission-group-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'permission-group-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No permission groups found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'name',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, PermissionGroup $model): string {
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', 'javascript:void(0);', [
                                    'class' => 'dropdown-item permission-group-update-button',
                                    'data-url' => Url::to(['users/permission-groups/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item permission-group-delete-button',
                                    'data-url' => Url::to(['users/permission-groups/delete', 'id' => $model->id]),
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

<div class="modal fade" id="permission-group-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permission-group-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="permission-group-modal-body"></div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openPermissionGroupModal(url, title) {
    var modal = $('#permission-group-modal');
    modal.find('.modal-title').text(title);
    modal.find('#permission-group-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#permission-group-modal-body').html(html);
    }).fail(function () {
        modal.find('#permission-group-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function showPermissionGroupToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#permission-group-message').html(toast);
    var toastEl = document.querySelector('#permission-group-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-permission-group-button', function (e) {
    e.preventDefault();
    openPermissionGroupModal($(this).data('url'), 'Add Permission Group');
});

$(document).on('click', '.permission-group-update-button', function (e) {
    e.preventDefault();
    openPermissionGroupModal($(this).data('url'), 'Update Permission Group');
});

$(document).on('click', '.permission-group-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this permission group';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#permission-group-grid-pjax'});
            showPermissionGroupToast(res.message || 'Permission group deleted successfully.', 'success');
            return;
        }
        showPermissionGroupToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showPermissionGroupToast('Unable to delete permission group.', 'error');
    });
});

$(document).on('submit', '#permission-group-form', function (e) {
    e.preventDefault();
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $('#permission-group-modal').modal('hide');
            $.pjax.reload({container: '#permission-group-grid-pjax'});
            showPermissionGroupToast(res.message || 'Saved successfully.', 'success');
            return;
        }

        if (res && res.html) {
            $('#permission-group-modal-body').html(res.html);
            return;
        }

        showPermissionGroupToast('Unable to save permission group.', 'error');
    }).fail(function () {
        showPermissionGroupToast('Unable to save permission group.', 'error');
    });

    return false;
});
JS
); ?>
