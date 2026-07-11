<?php

use app\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
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
            <?= Html::a('<i data-feather="plus-circle" class="me-2"></i> Add New User', ['create'], ['class' => 'btn btn-added']) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'user-index-message']) ?>

    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No users found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'username',
                    [
                        'label' => 'Name',
                        'value' => static function (User $model): string {
                            $profile = $model->profile;
                            if ($profile === null) {
                                return '-';
                            }
                            return trim($profile->first_name . ' ' . $profile->other_names);
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (User $model): string {
                            return match ((int) $model->status) {
                                User::STATUS_ACTIVE => '<span class="badge bg-success">Active</span>',
                                User::STATUS_INACTIVE => '<span class="badge bg-secondary">Not Activated</span>',
                                User::STATUS_BLOCKED => '<span class="badge bg-warning">Blocked</span>',
                                User::STATUS_BANNED => '<span class="badge bg-danger">Banned</span>',
                                default => '<span class="badge bg-dark">Unknown</span>',
                            };
                        },
                    ],
                    'login_attempts',
                    [
                        'attribute' => 'last_login_at',
                        'value' => static fn(User $model): string => $model->last_login_at ?: '-',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'buttons' => [
                            'dropdown' => static function ($url, User $model) {
                                $activate = Html::a('<i data-feather="check-circle" class="info-img"></i> Activate User', 'javascript:void(0);', [
                                    'class' => 'dropdown-item user-activate-button',
                                    'data-url' => Url::to(['activate', 'id' => $model->id]),
                                ]);

                                $resendActivationPassword = Html::a('<i data-feather="refresh-cw" class="info-img"></i> Resend Activation Password', 'javascript:void(0);', [
                                    'class' => 'dropdown-item user-resend-activation-button',
                                    'data-url' => Url::to(['resend-activation-password', 'id' => $model->id]),
                                ]);

                                $block = Html::a('<i data-feather="slash" class="info-img"></i> Block User', 'javascript:void(0);', [
                                    'class' => 'dropdown-item open-remarks-modal',
                                    'data-url' => Url::to(['block', 'id' => $model->id]),
                                    'data-title' => 'Block User',
                                ]);

                                $ban = Html::a('<i data-feather="x-circle" class="info-img"></i> Ban User', 'javascript:void(0);', [
                                    'class' => 'dropdown-item open-remarks-modal',
                                    'data-url' => Url::to(['ban', 'id' => $model->id]),
                                    'data-title' => 'Ban User',
                                ]);

                                $items = '<li>' . $activate . '</li>';
                                $items .= '<li>' . $resendActivationPassword . '</li>';
                                $items .= '<li>' . $block . '</li>';
                                $items .= '<li>' . $ban . '</li>';

                                return Html::a('<i class="fa fa-ellipsis-v" aria-hidden="true"></i>', 'javascript:void(0);', [
                                    'class' => 'action-set',
                                    'data-bs-toggle' => 'dropdown',
                                ]) . Html::tag('ul', $items, ['class' => 'dropdown-menu']);
                            },
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>

<div class="modal fade" id="remarksModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="remarksModalLabel">Update User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="remarksForm" method="post">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    <div class="mb-3">
                        <label class="form-label" for="remarks">Remarks</label>
                        <input type="text" class="form-control" id="remarks" name="remarks" required placeholder="Enter remarks">
                    </div>
                    <button type="submit" class="btn btn-submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function showUserIndexToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';
    $('#user-index-message').html(toast);
    var toastEl = document.querySelector('#user-index-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

function submitUserAjaxAction(url, successMessage) {
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            showUserIndexToast(res.message || successMessage, 'success');
            window.location.reload();
            return;
        }
        showUserIndexToast((res && res.message) || 'Operation failed.', 'error');
    }).fail(function (jqXHR) {
        showUserIndexToast((jqXHR.responseJSON && jqXHR.responseJSON.message) ? jqXHR.responseJSON.message : 'Request failed.', 'error');
    });
}

$(document).on('click', '.open-remarks-modal', function (e) {
    e.preventDefault();
    var modal = $('#remarksModal');
    var url = $(this).data('url');
    var title = $(this).data('title');
    modal.find('#remarksForm').attr('action', url);
    modal.find('#remarks').val('');
    modal.find('#remarksModalLabel').text(title);
    modal.modal('show');
});

$(document).on('click', '.user-activate-button', function (e) {
    e.preventDefault();
    submitUserAjaxAction($(this).data('url'), 'User activated successfully.');
});

$(document).on('click', '.user-resend-activation-button', function (e) {
    e.preventDefault();
    submitUserAjaxAction($(this).data('url'), 'Activation password resent successfully and SMS queued.');
});
JS
); ?>
