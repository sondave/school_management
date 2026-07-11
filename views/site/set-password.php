<?php

/** @var yii\web\View $this */
/** @var app\models\SetPasswordForm $model */

use app\widgets\Alert;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Set New Password';
?>
<div class="login-content user-login">
    <div class="login-logo">
        <img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo.png" alt="img">
    </div>

    <?php $form = ActiveForm::begin(['id' => 'set-password-form']); ?>
        <?= Alert::widget(['options' => ['class' => 'mb-3']]) ?>

        <div class="login-userset">
            <div class="login-userheading">
                <h3>Set New Password</h3>
                <h4>For security, you must set a new password before continuing.</h4>
            </div>

            <div class="form-login">
                <?= $form->field($model, 'newPassword', [
                    'inputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'New password',
                    ],
                ])->passwordInput()->label('New Password') ?>
            </div>

            <div class="form-login">
                <?= $form->field($model, 'confirmPassword', [
                    'inputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirm new password',
                    ],
                ])->passwordInput()->label('Confirm Password') ?>
            </div>

            <div class="form-login">
                <?= Html::submitButton('Save Password', ['class' => 'btn btn-login']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
