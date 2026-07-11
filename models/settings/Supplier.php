<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_suppliers".
 *
 * @property int $id
 * @property string $name
 * @property string $source_type
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Supplier extends ActiveRecord
{
    public const SOURCE_TYPE_GOVERNMENT = 'Government';
    public const SOURCE_TYPE_DONOR = 'Donor';
    public const SOURCE_TYPE_SUPPLIER = 'Supplier';
    public const SOURCE_TYPE_NGO = 'NGO';

    public static function tableName(): string
    {
        return 'st_suppliers';
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
            [['name', 'source_type'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['name', 'email', 'address'], 'string', 'max' => 255],
            [['source_type'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 30],
            [['phone', 'email', 'address', 'name'], 'trim'],
            [['phone', 'email', 'address'], 'default', 'value' => null],
            [['source_type'], 'in', 'range' => array_keys(self::getSourceTypeOptions())],
            [['email'], 'email'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'source_type' => 'Source Type',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getSourceTypeOptions(): array
    {
        return [
            self::SOURCE_TYPE_GOVERNMENT => 'Government',
            self::SOURCE_TYPE_DONOR => 'Donor',
            self::SOURCE_TYPE_SUPPLIER => 'Supplier',
            self::SOURCE_TYPE_NGO => 'NGO',
        ];
    }

    public function getSourceTypeLabel(): string
    {
        return self::getSourceTypeOptions()[$this->source_type] ?? $this->source_type;
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