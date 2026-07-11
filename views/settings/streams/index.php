<?php

use app\models\settings\Stream;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Streams';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="streams-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Stream', [
                'class' => 'btn btn-added',
                'id' => 'create-stream-button',
                'data-url' => Url::to(['settings/streams/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'stream-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'stream-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No streams found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'stream',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Stream $model): string {
                            return (int) $model->status === Stream::STATUS_ACTIVE
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
                            'dropdown' => static function ($url, Stream $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item stream-view-button',
                                    'data-url' => Url::to(['settings/streams/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item stream-update-button',
                                    'data-url' => Url::to(['settings/streams/update', 'id' => $model->id]),
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

<div class="modal fade" id="stream-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stream-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="stream-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="streamViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="streamViewModalLabel">Stream Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="streamViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openStreamModal(url, title) {
    var modal = $('#stream-modal');
    modal.find('.modal-title').text(title);
    modal.find('#stream-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#stream-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#stream-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openStreamViewModal(url) {
    var modal = $('#streamViewModal');
    modal.find('#streamViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#streamViewModalBody').html(html);
    }).fail(function () {
        modal.find('#streamViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showStreamToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#stream-message').html(toast);
    var toastEl = document.querySelector('#stream-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-stream-button', function (e) {
    e.preventDefault();
    openStreamModal($(this).data('url'), 'Add Stream');
});

$(document).on('click', '.stream-update-button', function (e) {
    e.preventDefault();
    openStreamModal($(this).data('url'), 'Update Stream');
});

$(document).on('click', '.stream-view-button', function (e) {
    e.preventDefault();
    openStreamViewModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#stream-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#stream-modal').modal('hide');
                $.pjax.reload({container: '#stream-grid-pjax'});
                showStreamToast(res.message || 'Stream saved successfully.', 'success');
            } else if (res.html) {
                $('#stream-modal-body').html(res.html);
            } else {
                showStreamToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showStreamToast('Unable to save stream.', 'error');
        }
    });

    return false;
});
JS
); ?>
