<?php

use app\models\settings\SmsTemplate;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\settings\SmsTemplateSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'SMS Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-index">

    <!-- Page Header -->
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add SMS Template', [
                'class' => 'btn btn-added',
                'id' => 'create-sms-template-button',
                'data-url' => Url::to(['settings/sms-template/create']),
            ]) ?>
        </div>
    </div>
    <!-- /Page Header -->

    <?= Html::tag('div', '', ['id' => 'sms-template-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin([
                'id' => 'sms-template-grid-pjax',
                'enablePushState' => true,
                'timeout' => 5000,
            ]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                'emptyText' => 'No SMS templates available.',
                'emptyTextOptions' => [
                    'class' => 'text-center text-muted p-4 custom-empty-grid-row',
                ],
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                    'id' => 'sms-template-datatable',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'name',
                        'label' => 'Primary Module',
                        'value' => function ($model) {
                            return $model->getPrimaryKeyLabel();
                        },
                    ],
                    [
                        'attribute' => 'name',
                        'label' => 'Alert Type',
                        'value' => function ($model) {
                            return strtolower(str_replace('_', ' ', $model->name));
                        },
                    ],
                    [
                        'attribute' => 'template',
                        'format' => 'raw',
                        'contentOptions' => [
                            'style' => 'white-space: normal !important; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;'
                        ],
                    ],

                    'description',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function (SmsTemplate $model) {
                            return $model->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                        },
                        'filter' => [1 => 'Active', 0 => 'Inactive'],
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => Yii::t('app', 'Actions'),
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => function ($url, $model, $key) {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item sms-template-view-button',
                                    'data-url' => Url::to(['settings/sms-template/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item sms-template-update-button',
                                    'data-url' => Url::to(['settings/sms-template/update', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', '#', [
                                    'class' => 'dropdown-item sms-template-delete-button',
                                    'data-url' => Url::to(['settings/sms-template/delete', 'id' => $model->id]),
                                ]) . '</li>';

                                return Html::a('<i class="fa fa-ellipsis-v" aria-hidden="true"></i>', 'javascript:void(0);', [
                                    'class' => 'action-set',
                                    'data-bs-toggle' => 'dropdown',
                                    'aria-expanded' => 'false',
                                ]) . Html::tag('ul', $items, ['class' => 'dropdown-menu']);
                            },
                        ],
                        'urlCreator' => function ($action, SmsTemplate $model) {
                            return Url::toRoute([$action, 'id' => $model->id]);
                        },
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>

        </div>
    </div>

</div>

<div class="modal fade" id="sms-template-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sms-template-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="sms-template-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="smsTemplateViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">SMS Template Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openSmsTemplateModal(url, title) {
    var modal = $('#sms-template-modal');
    modal.find('.modal-title').text(title);
    modal.find('#sms-template-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');
    $.get(url).done(function(html){
        modal.find('#sms-template-modal-body').html(html);
    }).fail(function(){
        modal.find('#sms-template-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openSmsTemplateViewModal(url) {
    var modal = $('#smsTemplateViewModal');
    modal.find('#viewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');
    $.get(url).done(function(html){
        modal.find('#viewModalBody').html(html);
    }).fail(function(){
        modal.find('#viewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showSmsTemplateToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';
    $('#sms-template-message').html(toast);
    var toastEl = document.querySelector('#sms-template-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-sms-template-button', function(e) {
    e.preventDefault();
    openSmsTemplateModal($(this).data('url'), 'Add SMS Template');
});

$(document).on('click', '.sms-template-update-button', function(e) {
    e.preventDefault();
    openSmsTemplateModal($(this).data('url'), 'Update SMS Template');
});

$(document).on('click', '.sms-template-view-button', function(e) {
    e.preventDefault();
    openSmsTemplateViewModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#sms-template-form', function() {
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                $('#sms-template-modal').modal('hide');
                $.pjax.reload({container: '#sms-template-grid-pjax'});
                showSmsTemplateToast(res.message || 'SMS template saved successfully.', 'success');
            } else if (res.html) {
                $('#sms-template-modal-body').html(res.html);
            } else {
                showSmsTemplateToast('An unexpected error occurred.', 'error');
            }
        },
        error: function() {
            showSmsTemplateToast('Unable to save template.', 'error');
        }
    });
    return false;
});

$(document).on('click', '.sms-template-delete-button', function(e) {
    e.preventDefault();

    if (!confirm('Are you sure you want to delete this template?')) {
        return false;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        success: function(res) {
            if (res.success) {
                $.pjax.reload({container: '#sms-template-grid-pjax'});
                showSmsTemplateToast(res.message || 'SMS template deleted successfully.', 'success');
            } else {
                showSmsTemplateToast('Delete failed.', 'error');
            }
        },
        error: function() {
            showSmsTemplateToast('Unable to delete template.', 'error');
        }
    });
});
JS
); ?>
