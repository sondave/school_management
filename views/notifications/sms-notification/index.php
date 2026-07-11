<?php

use app\models\notifications\SmsNotification;
use app\models\notifications\SmsNotificationDispatchForm;
use app\models\settings\SmsTemplate;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\notifications\SmsNotificationDispatchForm $model */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array<int,array{id:int,name:string,template:string}> $templates */

$this->title = 'SMS Notifications';
$this->params['breadcrumbs'][] = $this->title;

$templateMap = [];
foreach ($templates as $template) {
    $templateMap[(int) $template['id']] = [
        'name' => SmsTemplate::resolveNameLabel((string) $template['name']),
        'template' => (string) $template['template'],
    ];
}
?>

<div class="sms-notification-index">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <h6>Queue SMS notifications and send in background workers.</h6>
            </div>
        </div>
        <div class="page-btn">
            <?= Html::button('<i data-feather="plus-circle" class="me-2"></i> Queue SMS', [
                'class' => 'btn btn-added',
                'id' => 'open-sms-modal-button',
                'type' => 'button',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#sms-dispatch-modal',
            ]) ?>
        </div>
    </div>

    <?php if (\Yii::$app->session->hasFlash('success')) : ?>
        <div class="alert alert-success"><?= Html::encode((string) \Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>

    <?php if (\Yii::$app->session->hasFlash('error')) : ?>
        <div class="alert alert-danger"><?= Html::encode((string) \Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Recent SMS Notifications</h5>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover'],
                'layout' => "<div class='table-responsive'>{items}</div><div class='d-flex justify-content-between align-items-center mt-3'>{summary}{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    'tracking_id',
                    [
                        'attribute' => 'phone_number',
                        'label' => 'Phone',
                    ],
                    [
                        'attribute' => 'recipient_type',
                        'value' => static function (SmsNotification $row): string {
                            return SmsNotificationDispatchForm::getRecipientTypeOptions()[$row->recipient_type] ?? (string) $row->recipient_type;
                        },
                    ],
                    [
                        'attribute' => 'sms_template_id',
                        'label' => 'Template',
                        'value' => static fn(SmsNotification $row): string => $row->template?->getNameLabel() ?? '-',
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => static function (SmsNotification $row): string {
                            $badge = 'secondary';
                            if ((int) $row->status === SmsNotification::STATUS_SENT) {
                                $badge = 'success';
                            } elseif ((int) $row->status === SmsNotification::STATUS_FAILED) {
                                $badge = 'danger';
                            } elseif ((int) $row->status === SmsNotification::STATUS_SUBMITTED) {
                                $badge = 'info';
                            }
                            return '<span class="badge bg-' . $badge . '">' . Html::encode($row->getStatusLabel()) . '</span>';
                        },
                    ],
                    'created_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>

<div class="modal fade" id="sms-dispatch-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Queue SMS Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'sms-notification-form',
                    'enableClientValidation' => true,
                    'enableAjaxValidation' => true,
                    'validateOnBlur' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'errorCssClass' => 'is-invalid',
                    'validationStateOn' => ActiveForm::VALIDATION_STATE_ON_INPUT,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'labelOptions' => ['class' => 'form-label'],
                        'inputOptions' => ['class' => 'form-control'],
                        'errorOptions' => ['class' => 'invalid-feedback'],
                    ],
                ]); ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <?= $form->field($model, 'recipient_type')->dropDownList(
                            SmsNotificationDispatchForm::getRecipientTypeOptions(),
                            ['prompt' => '-- Select Target --', 'class' => 'form-select']
                        ) ?>
                    </div>

                    <div class="col-md-4 mb-3 recipient-grade d-none">
                        <?= $form->field($model, 'grade_id')->dropDownList(
                            SmsNotificationDispatchForm::getGradeOptionsWithStudents(),
                            ['prompt' => '-- Select Grade --', 'class' => 'form-select']
                        ) ?>
                    </div>

                    <div class="col-md-4 mb-3 recipient-parent d-none">
                        <?= $form->field($model, 'parent_id')->dropDownList(
                            SmsNotificationDispatchForm::getParentOptions(),
                            ['prompt' => '-- Select Parent --', 'class' => 'form-select']
                        ) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <?= $form->field($model, 'sms_template_id')->dropDownList(
                            SmsNotificationDispatchForm::getTemplateOptions(),
                            ['prompt' => '-- Select SMS Template --', 'class' => 'form-select']
                        ) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <?= $form->field($model, 'message')->textarea([
                        'rows' => 5,
                        'placeholder' => 'Select a template. Known placeholders like {first_name} are auto-replaced from records.',
                    ]) ?>
                    <div class="small text-muted" id="template-params-hint">No template selected.</div>
                </div>

                <div class="form-group mt-3 d-flex justify-content-end gap-2">
                    <?= Html::button('Cancel', [
                        'class' => 'btn btn-secondary',
                        'type' => 'button',
                        'data-bs-dismiss' => 'modal',
                    ]) ?>
                    <?= Html::submitButton('Queue SMS', ['class' => 'btn btn-submit']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$templateJson = json_encode($templateMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$hasErrors = $model->hasErrors() ? 'true' : 'false';
$this->registerJs(<<<JS
(function () {
    var templates = {$templateJson} || {};
    var hasErrors = {$hasErrors};
    var recipientField = $('#smsnotificationdispatchform-recipient_type');
    var gradeWrap = $('.recipient-grade');
    var parentWrap = $('.recipient-parent');
    var templateField = $('#smsnotificationdispatchform-sms_template_id');
    var messageField = $('#smsnotificationdispatchform-message');
    var paramsHint = $('#template-params-hint');
    var modalElement = document.getElementById('sms-dispatch-modal');

    function updateRecipientFields() {
        var value = recipientField.val();
        gradeWrap.toggleClass('d-none', value !== 'by_grade');
        parentWrap.toggleClass('d-none', value !== 'specific_parent');
    }

    function extractPlaceholders(message) {
        var matches = String(message || '').match(/\{[a-zA-Z0-9_]+\}/g) || [];
        var unique = [];
        matches.forEach(function (item) {
            if (unique.indexOf(item) === -1) {
                unique.push(item);
            }
        });
        return unique;
    }

    function updateTemplateMessage() {
        var selectedId = templateField.val();
        var selected = templates[selectedId];

        if (!selected) {
            paramsHint.text('No template selected.');
            return;
        }

        messageField.val(selected.template || '');
        var placeholders = extractPlaceholders(selected.template || '');

        if (placeholders.length === 0) {
            paramsHint.text('This template has no parameters to populate.');
            return;
        }

        paramsHint.text('Populate these parameters in the message: ' + placeholders.join(', '));
    }

    recipientField.on('change', updateRecipientFields);
    templateField.on('change', updateTemplateMessage);

    updateRecipientFields();

    if (modalElement && hasErrors) {
        var smsModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        smsModal.show();
    }
})();
JS
);
?>
