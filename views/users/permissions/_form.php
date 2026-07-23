<?php

use app\models\users\Permission;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\users\Permission $model */
/** @var yii\widgets\ActiveForm $form */

$routeOptions = Permission::getRouteOptions();
$currentRoute = (string) $model->name;
if ($currentRoute !== '' && !isset($routeOptions[$currentRoute])) {
    $routeOptions = [$currentRoute => $currentRoute] + $routeOptions;
}
?>

<div class="permission-form">

    <?php $form = ActiveForm::begin([
        'id' => 'permission-form',
        'enableClientValidation' => true,
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validateOnChange' => true,
        'validateOnType' => false,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <div class="mb-3">
        <?= $form->field($model, 'name')->dropDownList(
            $routeOptions,
            ['class' => 'form-select select2 permission-route-select', 'prompt' => 'Select route']
        ) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'auth_item_group_id')->dropDownList(
            Permission::getGroupOptions(),
            ['class' => 'form-select select2 permission-group-select', 'prompt' => 'Select permission group']
        ) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'description')->textarea([
            'rows' => 3,
            'placeholder' => 'Name of the permission, e.g. View Users',
        ]) ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Save Permission' : 'Update Permission',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
