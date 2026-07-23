<?php

use app\models\users\Role;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\users\Role $model */
?>

<div class="role-permissions-view">
    <?php $permissionGroups = Role::getPermissionOptions(); ?>

    <div class="row g-3">
        <?php foreach ($permissionGroups as $groupName => $permissions): ?>
            <?php
            $assigned = [];
            foreach ($permissions as $permissionValue => $permissionLabel) {
                if (in_array((string) $permissionValue, $model->permissionNames, true)) {
                    $assigned[(string) $permissionValue] = (string) $permissionLabel;
                }
            }
            ?>
            <?php if ($assigned === []): ?>
                <?php continue; ?>
            <?php endif; ?>
            <div class="col-md-4">
                <div class="card border h-100">
                    <div class="card-header py-2">
                        <strong><?= Html::encode((string) $groupName) ?></strong>
                    </div>
                    <div class="card-body py-3">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($assigned as $permissionLabel): ?>
                                <li><?= Html::encode($permissionLabel) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($model->permissionNames === []): ?>
        <p class="text-muted mb-0">No permissions assigned.</p>
    <?php endif; ?>
</div>
