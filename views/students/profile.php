<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
/** @var app\models\StudentParent $parentModel */
/** @var app\models\StudentEnrollment $enrollmentModel */
/** @var string $activeTab */

$this->title = 'Student Profile: ' . $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tabs = ['profile', 'parents', 'enrollment'];
if (!in_array($activeTab, $tabs, true)) {
    $activeTab = 'profile';
}
?>
<div class="students-profile">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['students/index']) ?>">Students</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::a('Update Student', ['students/update', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
        </div>
    </div>

    <?php if (\Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Html::encode((string) \Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>
    <?php if (\Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode((string) \Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs mb-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'profile' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['students/profile', 'id' => $model->id, 'tab' => 'profile']) ?>">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'parents' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['students/profile', 'id' => $model->id, 'tab' => 'parents']) ?>">Parents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $activeTab === 'enrollment' ? 'active' : '' ?>" href="<?= \yii\helpers\Url::to(['students/profile', 'id' => $model->id, 'tab' => 'enrollment']) ?>">Enrollment</a>
                    </li>
                </ul>

                <div>
                    <?php if ($activeTab === 'parents'): ?>
                        <?= Html::button('Add Parent', [
                            'class' => 'btn btn-success',
                            'id' => 'student-add-tab-action-button',
                            'data-title' => 'Add Parent',
                            'data-url' => \yii\helpers\Url::to(['students/add-parent', 'id' => $model->id]),
                        ]) ?>
                    <?php elseif ($activeTab === 'enrollment'): ?>
                        <?= Html::button('Add Enrollment', [
                            'class' => 'btn btn-success',
                            'id' => 'student-add-tab-action-button',
                            'data-title' => 'Add Enrollment',
                            'data-url' => \yii\helpers\Url::to(['students/add-enrollment', 'id' => $model->id]),
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($activeTab === 'profile'): ?>
                <?= $this->render('_tab_profile', ['model' => $model]) ?>
            <?php elseif ($activeTab === 'parents'): ?>
                <?= $this->render('_tab_parents', ['model' => $model, 'parentModel' => $parentModel]) ?>
            <?php else: ?>
                <?= $this->render('_tab_enrollment', ['model' => $model, 'enrollmentModel' => $enrollmentModel]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="student-tab-action-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="student-tab-action-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="student-tab-action-modal-body"></div>
        </div>
    </div>
</div>

<?php
$profileParentsUrl = \yii\helpers\Url::to(['students/profile', 'id' => $model->id, 'tab' => 'parents']);
$profileEnrollmentUrl = \yii\helpers\Url::to(['students/profile', 'id' => $model->id, 'tab' => 'enrollment']);

$this->registerJs(<<<JS
function openStudentTabActionModal(url, title) {
    var modal = $('#student-tab-action-modal');
    modal.find('.modal-title').text(title);
    modal.find('#student-tab-action-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#student-tab-action-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#student-tab-action-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

$(document).on('click', '#student-add-tab-action-button', function (e) {
    e.preventDefault();
    openStudentTabActionModal($(this).data('url'), $(this).data('title'));
});

$(document).on('beforeSubmit', '#student-parent-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res && res.success) {
                window.location.href = '{$profileParentsUrl}';
                return;
            }

            if (res && res.html) {
                $('#student-tab-action-modal-body').html(res.html);
            }
        }
    });

    return false;
});

$(document).on('beforeSubmit', '#student-enrollment-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res && res.success) {
                window.location.href = '{$profileEnrollmentUrl}';
                return;
            }

            if (res && res.html) {
                $('#student-tab-action-modal-body').html(res.html);
            }
        }
    });

    return false;
});
JS
);
?>
