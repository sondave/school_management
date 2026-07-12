<?php
/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\widgets\Alert;

$htmlIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}</div>{error}{hint}
HTML;
$htmlPasswordIcon = <<<HTML
{label}<div class="input-group"><span class="input-group-text" aria-hidden="true">%s</span>{input}<button type="button" class="input-group-text" id="toggle-password" aria-label="Show password" title="Show password"><i id="toggle-password-icon" data-feather="eye"></i></button></div>{error}{hint}
HTML;
$labelOptions = ['class' => 'form-label fw-semibold small'];
?>

<div class="login-content user-login">
    <div class="login-logo">
        <img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo.jpg" alt="img">
        <a href="index.html" class="login-logo logo-white">
            <img src="<?= Yii::$app->request->baseUrl ?>/theme/img/logo-white.jpg"  alt="">
        </a>
    </div>
    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <?= Alert::widget(['options' => ['class' => 'mb-3']]) ?>
        <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
        <div class="login-userset">
            <div class="login-userheading">
                <h3>Sign In</h3>
                <h4>Access the application using your username and passcode.</h4>
            </div>

            <div class="form-login">
                <?= $form->field($model, 'username', [
                    'options' => ['class' => 'mb-0'],
                    'template' => sprintf($htmlIcon, '&#128100;'),
                    'inputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'username',
                        'autofocus' => true,
                    ],
                ])->textInput()->label('Your Username', $labelOptions) ?>
            </div>

            <div class="form-login">
                <?= $form->field($model, 'password', [
                    'options' => ['class' => 'mb-0'],
                    'template' => sprintf($htmlPasswordIcon, '&#128274;'),
                    'inputOptions' => [
                        'class' => 'form-control',
                        'placeholder' => 'Password',
                        'id' => 'loginform-password',
                    ],
                ])->passwordInput()->label('Your Password', $labelOptions) ?>
            </div>

            
            <div class="form-login authentication-check">
                <div class="row">
                    <div class="col-6">
                        <div class="custom-control custom-checkbox">
                            <label class="checkboxs ps-4 mb-0 pb-0 line-height-1">
                                <?= Html::activeCheckbox($model, 'rememberMe', ['label' => false]) ?>
                                <span class="checkmarks"></span> Remember me
                            </label>
                        </div>
                    </div>

                    <div class="col-6 text-end">
                        <?= \yii\helpers\Html::a(
                            'Forgot Password?',
                            ['site/request-password-reset'],
                            ['class' => 'forgot-link']
                        ) ?>
                    </div>
                </div>
            </div>

            <div class="form-login">
                <?= Html::submitButton(
                    'Sign In',
                    [
                        'class' => 'btn btn-login',
                        'name' => 'login-button',
                        'type' => 'submit'
                    ],
                ) ?>
            </div>
            
        </div>
    <?php ActiveForm::end(); ?>
    
</div>

<?php
$this->registerJs(<<<'JS'
$(document).on('click', '#toggle-password', function () {
    var passwordInput = $('#loginform-password');
    var icon = $('#toggle-password-icon');
    var isHidden = passwordInput.attr('type') === 'password';

    passwordInput.attr('type', isHidden ? 'text' : 'password');
    $(this).attr('aria-label', isHidden ? 'Hide password' : 'Show password');
    $(this).attr('title', isHidden ? 'Hide password' : 'Show password');

    if (typeof feather !== 'undefined') {
        icon.attr('data-feather', isHidden ? 'eye-off' : 'eye');
        feather.replace();
    }
});
JS
);
?>


