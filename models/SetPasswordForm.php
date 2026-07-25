<?php

declare(strict_types=1);

namespace app\models;

use yii\base\Model;
use yii\base\Security;

class SetPasswordForm extends Model
{
    public string $newPassword = '';
    public string $confirmPassword = '';

    public function __construct(private readonly User $user, $config = [])
    {
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['newPassword', 'confirmPassword'], 'required'],
            [['newPassword'], 'string', 'min' => 8, 'max' => 72],
            [['confirmPassword'], 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Passwords do not match.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'newPassword' => 'New Password',
            'confirmPassword' => 'Confirm Password',
        ];
    }

    public function resetPassword(Security $security): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->user->setPasswordHash($security->generatePasswordHash($this->newPassword));
        $this->user->status = User::STATUS_ACTIVE;
        $this->user->is_first_login = 0;
        $this->user->activation_pas_expires_at = null;
        $this->user->activated_at = date('Y-m-d H:i:s');
        $this->user->login_attempts = 0;

        return $this->user->save(false, [
            'password_hash',
            'status',
            'is_first_login',
            'activation_pas_expires_at',
            'activated_at',
            'login_attempts',
        ]);
    }
}
