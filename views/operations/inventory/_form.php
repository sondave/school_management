<?php

use app\models\operations\Inventory;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\operations\Inventory $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-form">

    <?php
    $inventoryItemOptions = empty($model->accesory_type)
        ? []
        : Inventory::getInventoryItemOptions((string) $model->accesory_type);
    ?>

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-form',
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
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'accesory_type')->dropDownList(
                    Inventory::getAccessoryTypeOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select accessory type']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'inventory_item_id')->dropDownList(
                    $inventoryItemOptions,
                    ['class' => 'form-select', 'prompt' => 'Select inventory item']
                ) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'supplier_id')->dropDownList(
                    Inventory::getSupplierOptions(),
                    ['class' => 'form-select', 'prompt' => 'Select supplier']
                ) ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'quantity')->input('number', ['min' => 0, 'placeholder' => 'Quantity']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'received_on')->input('date') ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => 255, 'placeholder' => 'Remarks']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="fas fa-save me-2"></i> Save Inventory Item'
                : '<i class="fas fa-save me-2"></i> Update Inventory Item',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$itemsByTypeUrl = Url::to(['operations/inventory/items-by-accessory-type']);
$selectedInventoryItemId = Json::htmlEncode((int) ($model->inventory_item_id ?? 0));
$this->registerJs(<<<JS
(function () {
    var accessoryField = $('#inventory-accesory_type');
    var itemField = $('#inventory-inventory_item_id');
    var endpoint = '{$itemsByTypeUrl}';
    var selectedInventoryItemId = {$selectedInventoryItemId};

    function buildOptions(items, selectedId) {
        var html = '<option value="">Select inventory item</option>';
        items.forEach(function (item) {
            var selected = Number(selectedId) === Number(item.id) ? ' selected' : '';
            html += '<option value="' + item.id + '"' + selected + '>' + $('<div>').text(item.label).html() + '</option>';
        });
        return html;
    }

    function loadItems(selectedId) {
        var accessoryType = accessoryField.val();
        if (!accessoryType) {
            itemField.html('<option value="">Select inventory item</option>').val('');
            return;
        }

        $.getJSON(endpoint, {accesoryType: accessoryType})
            .done(function (response) {
                var items = (response && response.options) ? response.options : [];
                itemField.html(buildOptions(items, selectedId));
            })
            .fail(function () {
                itemField.html('<option value="">Select inventory item</option>').val('');
            });
    }

    $(document).off('change.inventory-accessory').on('change.inventory-accessory', '#inventory-accesory_type', function () {
        loadItems(0);
    });

    if (accessoryField.val()) {
        loadItems(selectedInventoryItemId);
    }
})();
JS
);
?>