<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryDispersal $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-dispersal-receive-back-form">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-dispersal-receive-back-form',
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

    <?= $form->field($model, 'is_to_be_returned')->hiddenInput(['value' => 1])->label(false) ?>

    <div class="alert alert-info">
        <strong>Item:</strong> <?= Html::encode($model->getInventoryItemLabel()) ?><br>
        <strong>Qty Dispersed:</strong> <?= Html::encode((string) $model->qty_dispersed) ?>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'returned_on')->input('date') ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'qty_returned')->input('number', ['min' => 0, 'max' => (int) $model->qty_dispersed]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'missplaced')->input('number', ['readonly' => true, 'min' => 0]) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => 255, 'placeholder' => 'Remarks']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="fas fa-save me-2"></i> Save Return', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php $this->registerJs(<<<'JS'
(function () {
    var qtyDispersed = Number($('#inventorydispersal-qty_dispersed').val()) || 0;
    var qtyReturnedField = $('#inventorydispersal-qty_returned');
    var missplacedField = $('#inventorydispersal-missplaced');

    function recalculate() {
        var returned = Number(qtyReturnedField.val()) || 0;
        missplacedField.val(Math.max(0, qtyDispersed - returned));
    }

    $(document).off('input.inventory-dispersal-receive-back').on('input.inventory-dispersal-receive-back', '#inventorydispersal-qty_returned', recalculate);
    recalculate();
})();
JS
); ?>