<?php

use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\users\PermissionGroup $model */
?>

<div class="permission-group-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
        ],
    ]) ?>
</div>
