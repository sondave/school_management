<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\users\PermissionGroup $model */

$this->title = 'Create Permission Group';
$this->params['breadcrumbs'][] = ['label' => 'Permission Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="permission-groups-create">

    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= Url::to(['users/permission-groups/index']) ?>">Permission Groups</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?= $this->render('_form', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>

</div>
