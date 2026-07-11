<?php

use app\models\settings\Term;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Terms';
$this->params['breadcrumbs'][] = 'Settings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="terms-index">

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
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Add Term', [
                'class' => 'btn btn-added',
                'id' => 'create-term-button',
                'data-url' => Url::to(['settings/terms/create']),
            ]) ?>
        </div>
    </div>

    <?= Html::tag('div', '', ['id' => 'term-message']) ?>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'term-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No terms found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'academic_year_id',
                        'value' => static function (Term $model): string {
                            return $model->academicYear?->year ?? '-';
                        },
                    ],
                    'name',
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'current',
                        'format' => 'raw',
                        'value' => static function (Term $model): string {
                            return (int) $model->current === Term::CURRENT_YES
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
                            'dropdown' => static function ($url, Term $model): string {
                                $view = Html::a('<i data-feather="eye" class="info-img"></i> View', '#', [
                                    'class' => 'dropdown-item term-view-button',
                                    'data-url' => Url::to(['settings/terms/view', 'id' => $model->id]),
                                ]);
                                $update = Html::a('<i data-feather="edit" class="info-img"></i> Update', '#', [
                                    'class' => 'dropdown-item term-update-button',
                                    'data-url' => Url::to(['settings/terms/update', 'id' => $model->id]),
                                ]);
                                $delete = Html::a('<i data-feather="trash-2" class="info-img"></i> Delete', 'javascript:void(0);', [
                                    'class' => 'dropdown-item term-delete-button',
                                    'data-url' => Url::to(['settings/terms/delete', 'id' => $model->id]),
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

<div class="modal fade" id="term-modal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="term-modal-label"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="term-modal-body"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="termViewModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termViewModalLabel">Term Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="termViewModalBody">
                <div class="text-center py-3">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<'JS'
function openTermModal(url, title) {
    var modal = $('#term-modal');
    modal.find('.modal-title').text(title);
    modal.find('#term-modal-body').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#term-modal-body').html(html);
        if ($.fn.yiiActiveForm) {
            $.fn.yiiActiveForm.init();
        }
    }).fail(function () {
        modal.find('#term-modal-body').html('<div class="alert alert-danger">Unable to load form.</div>');
    });
}

function openTermViewModal(url) {
    var modal = $('#termViewModal');
    modal.find('#termViewModalBody').html('<div class="text-center py-4">Loading...</div>');
    modal.modal('show');

    $.get(url).done(function (html) {
        modal.find('#termViewModalBody').html(html);
    }).fail(function () {
        modal.find('#termViewModalBody').html('<div class="alert alert-danger">Unable to load details.</div>');
    });
}

function showTermToast(message, type) {
    var icon = type === 'success' ? 'las la-check-circle' : 'las la-exclamation-circle';
    var toast = '<div class="toast align-items-center text-white bg-' + (type === 'success' ? 'success' : 'danger') + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">'
        + '<div class="d-flex">'
        + '<div class="toast-body">'
        + '<i class="' + icon + ' me-2"></i>' + message
        + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>'
        + '</div>'
        + '</div>';

    $('#term-message').html(toast);
    var toastEl = document.querySelector('#term-message .toast');
    if (toastEl) {
        var toastObj = new bootstrap.Toast(toastEl, {delay: 5000});
        toastObj.show();
    }
}

$(document).on('click', '#create-term-button', function (e) {
    e.preventDefault();
    openTermModal($(this).data('url'), 'Add Term');
});

$(document).on('click', '.term-update-button', function (e) {
    e.preventDefault();
    openTermModal($(this).data('url'), 'Update Term');
});

$(document).on('click', '.term-view-button', function (e) {
    e.preventDefault();
    openTermViewModal($(this).data('url'));
});

$(document).on('click', '.term-delete-button', function (e) {
    e.preventDefault();
    var name = $(this).data('name') || 'this term';
    if (!confirm('Are you sure you want to delete ' + name + '?')) {
        return;
    }

    $.ajax({
        url: $(this).data('url'),
        type: 'POST',
        dataType: 'json'
    }).done(function (res) {
        if (res && res.success) {
            $.pjax.reload({container: '#term-grid-pjax'});
            showTermToast(res.message || 'Term deleted successfully.', 'success');
            return;
        }
        showTermToast((res && res.message) || 'Delete failed.', 'error');
    }).fail(function () {
        showTermToast('Unable to delete term.', 'error');
    });
});

$(document).on('beforeSubmit', '#term-form', function () {
    var form = $(this);

    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.success) {
                $('#term-modal').modal('hide');
                $.pjax.reload({container: '#term-grid-pjax'});
                showTermToast(res.message || 'Term saved successfully.', 'success');
            } else if (res.html) {
                $('#term-modal-body').html(res.html);
            } else {
                showTermToast('An unexpected error occurred.', 'error');
            }
        },
        error: function () {
            showTermToast('Unable to save term.', 'error');
        }
    });

    return false;
});
JS
); ?>
