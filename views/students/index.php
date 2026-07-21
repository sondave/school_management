<?php

use app\models\Student;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Students';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="students-index">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Student', [
                'class' => 'btn btn-added',
                'id' => 'create-student-button',
                'data-url' => \yii\helpers\Url::to(['students/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'student-message']) ?>

    <?php if (\Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Html::encode((string) \Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'student-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No students found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    
                    [
                        'label' => 'Full Name',
                        'format' => 'raw',
                        'value' => static fn(Student $model): string => Html::a(
                            Html::encode($model->getFullName()),
                            ['students/profile', 'id' => $model->id],
                            ['class' => 'text-primary fw-semibold']
                        ),
                    ],
                    [
                        'attribute' => 'gender_id',
                        'value' => static fn(Student $model): string => $model->getGenderLabel(),
                    ],
                    [
                        'label' => 'Grade',
                        'value' => static fn(Student $model): string => $model->currentEnrollment?->getGradeLabel() ?? '-',
                    ],
                    'date_of_birth:date',
                    'admission_date:date',
                    [
                        'attribute' => 'has_special_needs',
                        'value' => static fn(Student $model): string => $model->getHasSpecialNeedsLabel(),
                    ],
                    [
                        'attribute' => 'status',
                        'value' => static fn(Student $model): string => $model->getStatusLabel(),
                    ],
                    'upi',
                    'access_number',
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, Student $model): string {
                                $profile = Html::a('<i data-feather="eye" class="info-img"></i> Profile', ['students/profile', 'id' => $model->id], [
                                    'class' => 'dropdown-item',
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item student-update-button',
                                    'data-url' => \yii\helpers\Url::to(['students/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item student-delete-button',
                                    'data-url' => \yii\helpers\Url::to(['students/delete', 'id' => $model->id]),
                                    'data-name' => $model->getFullName(),
                                ]);

                                $items = '<li>' . $profile . '</li>';
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

<div class="modal fade" id="student-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="student-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="student-modal-body"></div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openStudentModal(url, title) {
    var modal = $('#student-modal');
    modal.find('.modal-title').text(title);
    modal.find('#student-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#student-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#student-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function showStudentToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#student-message').html(toast);
    var toastEl = document.querySelector('#student-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-student-button', function (e) {
    e.preventDefault();
    openStudentModal($(this).data('url'), 'Add Student');
});

$(document).on('click', '.student-update-button', function (e) {
    e.preventDefault();
    openStudentModal($(this).data('url'), 'Update Student');
});

$(document).on('click', '.student-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this student';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#student-grid-pjax'});
            showStudentToast(res.message || 'Student deleted successfully.', 'success');
            return;
        }
        showStudentToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showStudentToast('Unable to delete student.', 'error');
    });
});

$(document).on('beforeSubmit', '#student-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#student-modal').modal('hide');
                if (res.redirectUrl) {
                    window.location.href = res.redirectUrl;
                    return;
                }

                $.pjax.reload({container: '#student-grid-pjax'});
                showStudentToast(res.message || 'Student created successfully.', 'success');
            } else if (res.html) {
                $('#student-modal-body').html(res.html);
            } else {
                showStudentToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showStudentToast('Unable to save student.', 'error');
        }
    });

    return false;
});
JS
); ?>
