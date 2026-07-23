<?php

use app\models\users\Role;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\users\Role $model */
/** @var yii\widgets\ActiveForm $form */

$permissionGroups = Role::getPermissionOptions();
?>

<div class="role-form">

    <?php $form = ActiveForm::begin([
        'id' => 'role-form',
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
        <?= $form->field($model, 'name')->textInput([
            'maxlength' => true,
            'placeholder' => 'Enter role key e.g admin',
            'readonly' => !$model->isNewRecord,
        ]) ?>
    </div>

    <div class="mb-3">
        <?= $form->field($model, 'description')->textarea([
            'rows' => 3,
            'placeholder' => 'Optional description',
        ]) ?>
    </div>

    <div class="mb-3">
        <?= Html::activeHiddenInput($model, 'permissionNames', ['value' => '']) ?>
        <label class="form-label" for="role-permissions-group">Permissions</label>

        <div class="row g-3" id="role-permissions-group">
            <?php foreach ($permissionGroups as $groupName => $permissions): ?>
                <div class="col-md-4">
                    <div class="card h-100 border">
                        <div class="card-header py-2">
                            <strong><?= Html::encode((string) $groupName) ?></strong>
                        </div>
                        <div class="card-body py-3">
                            <?php if ($permissions === []): ?>
                                <span class="text-muted">No permissions in this group.</span>
                            <?php else: ?>
                                <?php foreach ($permissions as $permissionValue => $permissionLabel): ?>
                                    <div class="form-check mb-2">
                                        <?= Html::checkbox('Role[permissionNames][]', in_array((string) $permissionValue, $model->permissionNames, true), [
                                            'class' => 'form-check-input',
                                            'id' => 'role-permission-' . md5((string) $permissionValue),
                                            'value' => (string) $permissionValue,
                                        ]) ?>
                                        <?= Html::label((string) $permissionLabel, 'role-permission-' . md5((string) $permissionValue), ['class' => 'form-check-label']) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="invalid-feedback d-block"><?= Html::error($model, 'permissionNames') ?></div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Save Role' : 'Update Role',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']
        ) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary ms-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
