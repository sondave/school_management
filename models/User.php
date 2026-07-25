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
            [['username', 'email', 'password_hash', 'auth_key', 'access_token'], 'required'],
            [['username'], 'string', 'max' => 55],
            [['email'], 'string', 'max' => 100],
            [['password_hash'], 'string', 'max' => 150],
            [['auth_key', 'access_token'], 'string', 'max' => 199],
            [['status', 'is_first_login', 'login_attempts'], 'integer'],
            [['activation_pas_expires_at', 'last_login_at', 'blocked_at', 'activated_at', 'created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_INACTIVE],
            [['is_first_login'], 'default', 'value' => 1],
            [['login_attempts'], 'default', 'value' => 0],
            [['status'], 'in', 'range' => array_keys(self::getStatusList())],
            [['username'], 'unique'],
            [['email'], 'email'],
            [['email'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'status' => 'Status',
            'is_first_login' => 'First Login',
            'activation_pas_expires_at' => 'Activation Password Expires At',
            'last_login_at' => 'Last Login At',
            'login_attempts' => 'Login Attempts',
            'remarks' => 'Remarks',
            'blocked_at' => 'Blocked At',
            'activated_at' => 'Activated At',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
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

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->password_hash = $passwordHash;
    }

    public function getBlockedBannedAt(): ?string
    {
        return $this->blocked_at;
    }

    public function setBlockedBannedAt(?string $value): void
    {
        $this->blocked_at = $value;
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
