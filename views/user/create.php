<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\UserProfile $profile */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['user/index']) ?>">Users</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'user' => $user,
                'profile' => $profile,
            ]) ?>
        </div>
    </div>
</div>
