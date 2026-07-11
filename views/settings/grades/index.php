<?php

use app\models\settings\Grade;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Grades';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grades-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Grade', [
                'class' => 'btn btn-added',
                'id' => 'create-grade-button',
                'data-url' => Url::to(['settings/grades/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'grade-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'grade-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No grades found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'code',
                    'grade',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (Grade $model): string {
                            return (int) $model->status === Grade::STATUS_ACTIVE
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
                            'dropdown' => static function ($url, Grade $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item grade-view-button',
                                    'data-url' => Url::to(['settings/grades/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item grade-update-button',
                                    'data-url' => Url::to(['settings/grades/update', 'id' => $model->id]),
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

<div class="modal fade" id="grade-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grade-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="grade-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeViewModalLabel">Grade Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="gradeViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openGradeModal(url, title) {
    var modal = $('#grade-modal');
    modal.find('.modal-title').text(title);
    modal.find('#grade-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#grade-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#grade-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openGradeViewModal(url) {
    var modal = $('#gradeViewModal');
    modal.find('#gradeViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#gradeViewModalBody').html(html);
    }).fail(function () {
        modal.find('#gradeViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showGradeToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#grade-message').html(toast);
    var toastEl = document.querySelector('#grade-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-grade-button', function (e) {
    e.preventDefault();
    openGradeModal($(this).data('url'), 'Add Grade');
});

$(document).on('click', '.grade-update-button', function (e) {
    e.preventDefault();
    openGradeModal($(this).data('url'), 'Update Grade');
});

$(document).on('click', '.grade-view-button', function (e) {
    e.preventDefault();
    openGradeViewModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#grade-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#grade-modal').modal('hide');
                $.pjax.reload({container: '#grade-grid-pjax'});
                showGradeToast(res.message || 'Grade saved successfully.', 'success');
            } else if (res.html) {
                $('#grade-modal-body').html(res.html);
            } else {
                showGradeToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showGradeToast('Unable to save grade.', 'error');
        }
    });

    return false;
});
JS
); ?>
