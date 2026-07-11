<?php

use app\models\settings\GradeStream;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Grade Streams';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-streams-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Grade Stream', [
                'class' => 'btn btn-added',
                'id' => 'create-grade-stream-button',
                'data-url' => Url::to(['settings/grade-streams/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'grade-stream-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'grade-stream-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No grade streams found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'label' => 'Grade',
                        'value' => static fn(GradeStream $model): string => $model->grade?->grade ?? '-',
                    ],
                    [
                        'label' => 'Stream',
                        'value' => static fn(GradeStream $model): string => $model->stream?->stream ?? '-',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (GradeStream $model): string {
                            return (int) $model->status === GradeStream::STATUS_ACTIVE
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
                            'dropdown' => static function ($url, GradeStream $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item grade-stream-view-button',
                                    'data-url' => Url::to(['settings/grade-streams/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item grade-stream-update-button',
                                    'data-url' => Url::to(['settings/grade-streams/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item grade-stream-delete-button',
                                    'data-url' => Url::to(['settings/grade-streams/delete', 'id' => $model->id]),
                                    'data-name' => $model->grade?->grade . ' / ' . $model->stream?->stream,
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

<div class="modal fade" id="grade-stream-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grade-stream-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="grade-stream-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeStreamViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeStreamViewModalLabel">Grade Stream Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="gradeStreamViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openGradeStreamModal(url, title) {
    var modal = $('#grade-stream-modal');
    modal.find('.modal-title').text(title);
    modal.find('#grade-stream-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#grade-stream-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#grade-stream-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openGradeStreamViewModal(url) {
    var modal = $('#gradeStreamViewModal');
    modal.find('#gradeStreamViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#gradeStreamViewModalBody').html(html);
    }).fail(function () {
        modal.find('#gradeStreamViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showGradeStreamToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#grade-stream-message').html(toast);
    var toastEl = document.querySelector('#grade-stream-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-grade-stream-button', function (e) {
    e.preventDefault();
    openGradeStreamModal($(this).data('url'), 'Add Grade Stream');
});

$(document).on('click', '.grade-stream-update-button', function (e) {
    e.preventDefault();
    openGradeStreamModal($(this).data('url'), 'Update Grade Stream');
});

$(document).on('click', '.grade-stream-view-button', function (e) {
    e.preventDefault();
    openGradeStreamViewModal($(this).data('url'));
});

$(document).on('click', '.grade-stream-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this grade stream';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#grade-stream-grid-pjax'});
            showGradeStreamToast(res.message || 'Grade stream deleted successfully.', 'success');
            return;
        }
        showGradeStreamToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showGradeStreamToast('Unable to delete grade stream.', 'error');
    });
});

$(document).on('beforeSubmit', '#grade-stream-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#grade-stream-modal').modal('hide');
                $.pjax.reload({container: '#grade-stream-grid-pjax'});
                showGradeStreamToast(res.message || 'Grade stream saved successfully.', 'success');
            } else if (res.html) {
                $('#grade-stream-modal-body').html(res.html);
            } else {
                showGradeStreamToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showGradeStreamToast('Unable to save grade stream.', 'error');
        }
    });

    return false;
});
JS
); ?>
