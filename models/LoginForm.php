<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Security;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 */
class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = true;
    public bool $requiresPasswordReset = false;
    private User|null $_user = null;
    private bool $_userLoaded = false;
    public function __construct(private readonly Security $security, $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addError('password', 'Incorrect username or password.');
            return false;
        }

        if ((int) $user->status === User::STATUS_INACTIVE && (int) $user->is_first_login === 0) {
            $this->addError('password', 'Your account is deactivated, contact admin for activation.');
            return false;
        }

        if ((int) $user->status === User::STATUS_BLOCKED) {
            $this->addError('password', 'Your account is currently blocked.');
            return false;
        }

        if ((int) $user->status === User::STATUS_BANNED) {
            $this->addError('password', 'Your account is currently banned.');
            return false;
        }

        if (!$this->security->validatePassword($this->password, $user->passwordHash)) {
            $user->login_attempts = (int) $user->login_attempts + 1;

            if ((int) $user->login_attempts >= 3) {
                $user->status = User::STATUS_BLOCKED;
                $user->blocked_at = date('Y-m-d H:i:s');
                $user->remarks = '3 wrong login attempts';
                $user->save(false, ['login_attempts', 'status', 'blocked_at', 'remarks']);
                $this->addError('password', 'Your account is currently blocked after 3 wrong login attempts.');
                return false;
            }

            $user->save(false, ['login_attempts']);
            $remaining = max(0, 3 - (int) $user->login_attempts);
            $this->addError('password', 'Incorrect username or password. Attempts remaining: ' . $remaining . '.');
            return false;
        }

        if ((int) $user->status === User::STATUS_INACTIVE && (int) $user->is_first_login === 1) {
            $expiry = $user->activation_pas_expires_at;
            if ($expiry !== null && strtotime($expiry) > time()) {
                $this->requiresPasswordReset = true;
                return Yii::$app->user->login($user, 0);
            }

            $this->addError('password', 'Activation password has expired. Please contact admin to resend activation password.');
            return false;
        }

        $user->last_login_at = date('Y-m-d H:i:s');
        $user->is_first_login = 0;
        $user->login_attempts = 0;
        $user->activation_pas_expires_at = null;
        $user->save(false, ['last_login_at', 'is_first_login', 'login_attempts', 'activation_pas_expires_at']);

        return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(): User|null
    {
        if (!$this->_userLoaded) {
            $this->_user = User::findByUsername($this->username);
            $this->_userLoaded = true;
        }

        return $this->_user;
    }
}
