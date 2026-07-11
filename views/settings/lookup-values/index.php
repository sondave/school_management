<?php

use app\models\settings\LookupValue;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Lookup Values';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-values-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Lookup Value', [
                'class' => 'btn btn-added',
                'id' => 'create-lookup-value-button',
                'data-url' => Url::to(['settings/lookup-values/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'lookup-value-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'lookup-value-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No lookup values found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'category',
                        'value' => static function (LookupValue $model): string {
                            return $model->getCategoryLabel();
                        },
                    ],
                    'code',
                    'name',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (LookupValue $model): string {
                            return (int) $model->status === LookupValue::STATUS_ACTIVE
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
                            'dropdown' => static function ($url, LookupValue $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item lookup-value-view-button',
                                    'data-url' => Url::to(['settings/lookup-values/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item lookup-value-update-button',
                                    'data-url' => Url::to(['settings/lookup-values/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item lookup-value-delete-button',
                                    'data-url' => Url::to(['settings/lookup-values/delete', 'id' => $model->id]),
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

<div class="modal fade" id="lookup-value-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lookup-value-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="lookup-value-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="lookupValueViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lookupValueViewModalLabel">Lookup Value Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="lookupValueViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openLookupValueModal(url, title) {
    var modal = $('#lookup-value-modal');
    modal.find('.modal-title').text(title);
    modal.find('#lookup-value-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#lookup-value-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#lookup-value-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openLookupValueViewModal(url) {
    var modal = $('#lookupValueViewModal');
    modal.find('#lookupValueViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#lookupValueViewModalBody').html(html);
    }).fail(function () {
        modal.find('#lookupValueViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showLookupValueToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#lookup-value-message').html(toast);
    var toastEl = document.querySelector('#lookup-value-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-lookup-value-button', function (e) {
    e.preventDefault();
    openLookupValueModal($(this).data('url'), 'Add Lookup Value');
});

$(document).on('click', '.lookup-value-update-button', function (e) {
    e.preventDefault();
    openLookupValueModal($(this).data('url'), 'Update Lookup Value');
});

$(document).on('click', '.lookup-value-view-button', function (e) {
    e.preventDefault();
    openLookupValueViewModal($(this).data('url'));
});

$(document).on('click', '.lookup-value-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this lookup value';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#lookup-value-grid-pjax'});
            showLookupValueToast(res.message || 'Lookup value deleted successfully.', 'success');
            return;
        }
        showLookupValueToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showLookupValueToast('Unable to delete lookup value.', 'error');
    });
});

$(document).on('beforeSubmit', '#lookup-value-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#lookup-value-modal').modal('hide');
                $.pjax.reload({container: '#lookup-value-grid-pjax'});
                showLookupValueToast(res.message || 'Lookup value saved successfully.', 'success');
            } else if (res.html) {
                $('#lookup-value-modal-body').html(res.html);
            } else {
                showLookupValueToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showLookupValueToast('Unable to save lookup value.', 'error');
        }
    });

    return false;
});
JS
); ?>
