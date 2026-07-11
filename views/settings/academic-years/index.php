<?php

use app\models\settings\AcademicYear;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Academic Years';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-years-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Academic Year', [
                'class' => 'btn btn-added',
                'id' => 'create-academic-year-button',
                'data-url' => Url::to(['settings/academic-years/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'academic-year-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'academic-year-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No academic years found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'year',
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'current',
                        'format' => 'raw',
                        'value' => static function (AcademicYear $model): string {
                            return (int) $model->current === AcademicYear::CURRENT_YES
                                ? '<span class="badge bg-success">Current</span>'
                                : '<span class="badge bg-secondary">Not Current</span>';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, AcademicYear $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item academic-year-view-button',
                                    'data-url' => Url::to(['settings/academic-years/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item academic-year-update-button',
                                    'data-url' => Url::to(['settings/academic-years/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item academic-year-delete-button',
                                    'data-url' => Url::to(['settings/academic-years/delete', 'id' => $model->id]),
                                    'data-name' => $model->year,
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

<div class="modal fade" id="academic-year-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academic-year-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="academic-year-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="academicYearViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="academicYearViewModalLabel">Academic Year Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="academicYearViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openAcademicYearModal(url, title) {
    var modal = $('#academic-year-modal');
    modal.find('.modal-title').text(title);
    modal.find('#academic-year-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#academic-year-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#academic-year-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openAcademicYearViewModal(url) {
    var modal = $('#academicYearViewModal');
    modal.find('#academicYearViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#academicYearViewModalBody').html(html);
    }).fail(function () {
        modal.find('#academicYearViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showAcademicYearToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#academic-year-message').html(toast);
    var toastEl = document.querySelector('#academic-year-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-academic-year-button', function (e) {
    e.preventDefault();
    openAcademicYearModal($(this).data('url'), 'Add Academic Year');
});

$(document).on('click', '.academic-year-update-button', function (e) {
    e.preventDefault();
    openAcademicYearModal($(this).data('url'), 'Update Academic Year');
});

$(document).on('click', '.academic-year-view-button', function (e) {
    e.preventDefault();
    openAcademicYearViewModal($(this).data('url'));
});

$(document).on('click', '.academic-year-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this academic year';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#academic-year-grid-pjax'});
            showAcademicYearToast(res.message || 'Academic year deleted successfully.', 'success');
            return;
        }
        showAcademicYearToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showAcademicYearToast('Unable to delete academic year.', 'error');
    });
});

$(document).on('beforeSubmit', '#academic-year-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#academic-year-modal').modal('hide');
                $.pjax.reload({container: '#academic-year-grid-pjax'});
                showAcademicYearToast(res.message || 'Academic year saved successfully.', 'success');
            } else if (res.html) {
                $('#academic-year-modal-body').html(res.html);
            } else {
                showAcademicYearToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showAcademicYearToast('Unable to save academic year.', 'error');
        }
    });

    return false;
});
JS
); ?>
