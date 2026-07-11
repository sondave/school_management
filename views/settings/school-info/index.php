<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\settings\SchoolInfo|null $existingSchoolInfo */

$this->title = 'School Info';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-info-index">

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
            <?= Html::button(
                $existingSchoolInfo
                    ? '<i data-feather="edit" class="me-2"></i> Update School Info'
                    : '<i data-feather="plus-circle" class="me-2"></i> Add School Info',
                [
                    'class' => $existingSchoolInfo ? 'btn btn-info' : 'btn btn-added',
                    'id' => 'create-school-info-button',
                    'data-url' => $existingSchoolInfo
                        ? Url::to(['settings/school-info/update', 'id' => $existingSchoolInfo->id])
                        : Url::to(['settings/school-info/create']),
                ]
            ) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'school-info-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'school-info-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No school information found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'name',
                    'school_type',
                    'phone_number',
                    'county',
                    [
                        'attribute' => 'website',
                        'format' => 'url',
                        'value' => static fn($model): ?string => $model->website,
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{dropdown}',
                        'header' => 'Actions',
                        'headerOptions' => ['class' => 'text-center'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'dropdown' => static function ($url, $model): string {
                                $items = '<li>' . Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item school-info-view-button',
                                    'data-url' => Url::to(['settings/school-info/view', 'id' => $model->id]),
                                ]) . '</li>';
                                $items .= '<li>' . Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item school-info-update-button',
                                    'data-url' => Url::to(['settings/school-info/update', 'id' => $model->id]),
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

<div class="modal fade" id="school-info-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="school-info-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="school-info-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="schoolInfoViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="schoolInfoViewModalLabel">School Info Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="schoolInfoViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openSchoolInfoModal(url, title) {
    var modal = $('#school-info-modal');
    modal.find('.modal-title').text(title);
    modal.find('#school-info-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        if (typeof html === 'object' && html.redirectUrl) {
            modal.modal('hide');
            openSchoolInfoModal(html.redirectUrl, 'Update School Info');
            return;
        }
        modal.find('#school-info-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#school-info-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openSchoolInfoViewModal(url) {
    var modal = $('#schoolInfoViewModal');
    modal.find('#schoolInfoViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#schoolInfoViewModalBody').html(html);
    }).fail(function () {
        modal.find('#schoolInfoViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showSchoolInfoToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#school-info-message').html(toast);
    var toastEl = document.querySelector('#school-info-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-school-info-button', function (e) {
    e.preventDefault();
    var url = $(this).data('url');
    var title = url.indexOf('/update') !== -1 ? 'Update School Info' : 'Add School Info';
    openSchoolInfoModal(url, title);
});

$(document).on('click', '.school-info-update-button', function (e) {
    e.preventDefault();
    openSchoolInfoModal($(this).data('url'), 'Update School Info');
});

$(document).on('click', '.school-info-view-button', function (e) {
    e.preventDefault();
    openSchoolInfoViewModal($(this).data('url'));
});

$(document).on('beforeSubmit', '#school-info-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#school-info-modal').modal('hide');
                $.pjax.reload({container: '#school-info-grid-pjax'});
                showSchoolInfoToast(res.message || 'School information saved successfully.', 'success');
            } else if (res.redirectUrl) {
                $('#school-info-modal').modal('hide');
                openSchoolInfoModal(res.redirectUrl, 'Update School Info');
            } else if (res.html) {
                $('#school-info-modal-body').html(res.html);
            } else {
                showSchoolInfoToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showSchoolInfoToast('Unable to save school information.', 'error');
        }
    });

    return false;
});
JS
); ?>
