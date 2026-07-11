<?php

use app\models\settings\GradeSubject;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Grade Subjects';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-subjects-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Grade Subject', [
                'class' => 'btn btn-added',
                'id' => 'create-grade-subject-button',
                'data-url' => Url::to(['settings/grade-subjects/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'grade-subject-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'grade-subject-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No grade subjects found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'label' => 'Grade',
                        'value' => static fn(GradeSubject $model): string => $model->grade?->grade ?? '-',
                    ],
                    [
                        'label' => 'Subject',
                        'value' => static fn(GradeSubject $model): string => $model->subject?->name ?? '-',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (GradeSubject $model): string {
                            return (int) $model->status === GradeSubject::STATUS_ACTIVE
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
                            'dropdown' => static function ($url, GradeSubject $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item grade-subject-view-button',
                                    'data-url' => Url::to(['settings/grade-subjects/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item grade-subject-update-button',
                                    'data-url' => Url::to(['settings/grade-subjects/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item grade-subject-delete-button',
                                    'data-url' => Url::to(['settings/grade-subjects/delete', 'id' => $model->id]),
                                    'data-name' => $model->grade?->grade . ' / ' . $model->subject?->name,
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

<div class="modal fade" id="grade-subject-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="grade-subject-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="grade-subject-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeSubjectViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeSubjectViewModalLabel">Grade Subject Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="gradeSubjectViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openGradeSubjectModal(url, title) {
    var modal = $('#grade-subject-modal');
    modal.find('.modal-title').text(title);
    modal.find('#grade-subject-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#grade-subject-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#grade-subject-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openGradeSubjectViewModal(url) {
    var modal = $('#gradeSubjectViewModal');
    modal.find('#gradeSubjectViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#gradeSubjectViewModalBody').html(html);
    }).fail(function () {
        modal.find('#gradeSubjectViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showGradeSubjectToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#grade-subject-message').html(toast);
    var toastEl = document.querySelector('#grade-subject-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-grade-subject-button', function (e) {
    e.preventDefault();
    openGradeSubjectModal($(this).data('url'), 'Add Grade Subject');
});

$(document).on('click', '.grade-subject-update-button', function (e) {
    e.preventDefault();
    openGradeSubjectModal($(this).data('url'), 'Update Grade Subject');
});

$(document).on('click', '.grade-subject-view-button', function (e) {
    e.preventDefault();
    openGradeSubjectViewModal($(this).data('url'));
});

$(document).on('click', '.grade-subject-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this grade subject';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#grade-subject-grid-pjax'});
            showGradeSubjectToast(res.message || 'Grade subject deleted successfully.', 'success');
            return;
        }
        showGradeSubjectToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showGradeSubjectToast('Unable to delete grade subject.', 'error');
    });
});

$(document).on('beforeSubmit', '#grade-subject-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#grade-subject-modal').modal('hide');
                $.pjax.reload({container: '#grade-subject-grid-pjax'});
                showGradeSubjectToast(res.message || 'Grade subject saved successfully.', 'success');
            } else if (res.html) {
                $('#grade-subject-modal-body').html(res.html);
            } else {
                showGradeSubjectToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showGradeSubjectToast('Unable to save grade subject.', 'error');
        }
    });

    return false;
});
JS
); ?>
