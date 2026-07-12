<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_fee_categories".
 *
 * @property int $id
 * @property string $name
 * @property int $is_optional
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class FeesCategory extends ActiveRecord
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'st_fee_categories';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'status'], 'required'],
            [['is_optional', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
            [['name'], 'unique'],
            [['is_optional'], 'default', 'value' => 0],
            [['is_optional'], 'in', 'range' => [0, 1]],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_optional' => 'Is Optional',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    public static function getActiveOptions(): array
    {
        return self::find()
            ->select(['name', 'id'])
            ->where(['status' => self::STATUS_ACTIVE])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
    }

    public function getIsOptionalLabel(): string
    {
        return (int) $this->is_optional === 1 ? 'Yes' : 'No';
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
