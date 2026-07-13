<?php

use app\models\fees\FeePayment;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Fee Payments';
$this->params['breadcrumbs'][] = ['label' => 'School Fees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fee-payments-index">
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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Post Payment', [
                'class' => 'btn btn-added',
                'id' => 'create-fee-payment-button',
                'data-url' => Url::to(['settings/fee-payments/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'fee-payment-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'fee-payment-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No payments found.',
                'tableOptions' => ['class' => 'table datanew no-footer table-hover'],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'receipt_no',
                    [
                        'attribute' => 'student_id',
                        'value' => static fn(FeePayment $model): string => $model->student?->getFullName() ?? '-',
                    ],
                    [
                        'label' => 'Academic Year',
                        'value' => static fn(FeePayment $model): string => $model->getAcademicYearLabel(),
                    ],
                    [
                        'label' => 'Term',
                        'value' => static fn(FeePayment $model): string => $model->getTermLabel(),
                    ],
                    [
                        'label' => 'Grade',
                        'value' => static fn(FeePayment $model): string => $model->getGradeLabel(),
                    ],
                    'payment_date:date',
                    'payment_method',
                    [
                        'attribute' => 'amount',
                        'format' => ['decimal', 2],
                    ],
                    // 'created_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => static fn(FeePayment $model): string => $model->createdByUser?->username ?? '-',
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, FeePayment $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View Allocations', '#', [
                                    'class' => 'dropdown-item fee-payment-view-allocations-button',
                                    'data-url' => Url::to(['settings/fee-payments/view-allocations', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="printer" class="info-img"></i> Receipt', Url::to(['settings/fee-payments/receipt', 'id' => $model->id]), [
                                    'class' => 'dropdown-item',
                                    'target' => '_blank',
                                    'rel' => 'noopener',
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

<div class="modal fade" id="fee-payment-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fee-payment-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="fee-payment-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="feePaymentAllocationsModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feePaymentAllocationsModalLabel">Payment Allocations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feePaymentAllocationsModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openFeePaymentModal(url, title) {
    var modal = $('#fee-payment-modal');
    modal.find('.modal-title').text(title);
    modal.find('#fee-payment-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#fee-payment-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#fee-payment-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openFeePaymentAllocationsModal(url) {
    var modal = $('#feePaymentAllocationsModal');
    modal.find('#feePaymentAllocationsModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#feePaymentAllocationsModalBody').html(html);
    }).fail(function () {
        modal.find('#feePaymentAllocationsModalBody').html('<div class="alert alert-danger">Unable to load allocations.</div>');
    });
}

function showFeePaymentToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#fee-payment-message').html(toast);
    var toastEl = document.querySelector('#fee-payment-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-fee-payment-button', function (e) {
    e.preventDefault();
    openFeePaymentModal($(this).data('url'), 'Post Payment');
});

$(document).on('click', '.fee-payment-view-allocations-button', function (e) {
    e.preventDefault();
    openFeePaymentAllocationsModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#fee-payment-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res && res.success) {
                $('#fee-payment-modal').modal('hide');
                $.pjax.reload({container: '#fee-payment-grid-pjax'});
                showFeePaymentToast(res.message || 'Payment posted successfully.', 'success');
                return;
            }

            if (res && res.html) {
                $('#fee-payment-modal-body').html(res.html);
                if (res.message) {
                    showFeePaymentToast(res.message, 'error');
                }
                return;
            }

            showFeePaymentToast((res && res.message) || 'Unable to post payment.', 'error');
        },
        error: function () {
            showFeePaymentToast('Unable to post payment.', 'error');
        }
    });

    return false;
});
JS
); ?>
