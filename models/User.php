<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_BLOCKED = 2;
    public const STATUS_BANNED = 3;

    public static function tableName(): string
    {
        return 'user';
    }

    public function rules(): array
    {
        return [
            [['username', 'passwordHash', 'auth_key', 'access_token'], 'required'],
            [['username'], 'string', 'max' => 55],
            [['passwordHash', 'auth_key', 'access_token'], 'string', 'max' => 255],
            [['status', 'is_first_login', 'login_attempts'], 'integer'],
            [['activation_pas_expires_at', 'last_login_at', 'blocked_banned_at'], 'safe'],
            [['remarks'], 'string', 'max' => 255],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['is_first_login'], 'default', 'value' => 1],
            [['login_attempts'], 'default', 'value' => 0],
            [['status'], 'in', 'range' => array_keys(self::getStatusList())],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'passwordHash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'is_first_login' => 'First Login',
            'activation_pas_expires_at' => 'Activation Password Expires At',
            'last_login_at' => 'Last Login At',
            'login_attempts' => 'Login Attempts',
            'remarks' => 'Remarks',
            'blocked_banned_at' => 'Blocked/Banned At',
        ];
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): static|null
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): static|null
    {
        return self::find()->where(['access_token' => $token])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username): static|null
    {
        return self::findOne(['username' => $username]);
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_INACTIVE => 'Not Activated',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_BLOCKED => 'Blocked',
            self::STATUS_BANNED => 'Banned',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusList()[(int) $this->status] ?? 'Unknown';
    }

    public function getProfile()
    {
        return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string|null
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }



}
