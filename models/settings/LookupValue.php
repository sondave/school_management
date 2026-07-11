<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_lookup_values".
 *
 * @property int $id
 * @property string $category
 * @property string $code
 * @property string $name
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class LookupValue extends ActiveRecord
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'st_lookup_values';
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
            [['category', 'code', 'name', 'status'], 'required'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['category'], 'string', 'max' => 20],
            [['code'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 150],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['category'], 'in', 'range' => array_keys(self::getCategoryOptions())],
            [['category', 'code', 'name'], 'unique', 'targetAttribute' => ['category', 'code', 'name'], 'message' => 'This category, code, and name combination already exists.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'code' => 'Code',
            'name' => 'Name',
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

    public static function getCategoryOptions(): array
    {
        return [
            'gender' => 'Gender',
            'relationship' => 'Relationship',
            'marital_status' => 'Marital Status',
            'student_status' => 'Student Status',
            'teacher_status' => 'Teacher Status',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
    }

    public function getCategoryLabel(): string
    {
        return self::getCategoryOptions()[(string) $this->category] ?? (string) $this->category;
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
