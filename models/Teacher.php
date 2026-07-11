<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\LookupValue;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "teachers".
 *
 * @property int $id
 * @property string $first_name
 * @property string $other_names
 * @property string $phone_number
 * @property string|null $alternate_phone_number
 * @property string $email_address
 * @property string $date_of_birth
 * @property string $employment_type
 * @property string|null $tsc_number
 * @property string|null $staff_number
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Teacher extends ActiveRecord
{
    public const EMPLOYMENT_TYPE_TSC = 'TSC';
    public const EMPLOYMENT_TYPE_BOM = 'BOM';
    public const EMPLOYMENT_TYPE_PRIVATE = 'PRIVATE';
    public const EMPLOYMENT_TYPE_NGO = 'NGO';
    public const DEFAULT_STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'teachers';
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
            [['first_name', 'other_names', 'phone_number', 'email_address', 'date_of_birth', 'employment_type', 'status'], 'required'],
            [['date_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by', 'status'], 'integer'],
            [['first_name'], 'string', 'max' => 100],
            [['other_names'], 'string', 'max' => 150],
            [['phone_number', 'alternate_phone_number'], 'string', 'max' => 20],
            [['email_address'], 'string', 'max' => 255],
            [['tsc_number', 'staff_number'], 'string', 'max' => 50],
            [['phone_number', 'alternate_phone_number', 'email_address', 'tsc_number', 'staff_number'], 'trim'],
            [['alternate_phone_number', 'staff_number', 'tsc_number'], 'default', 'value' => null],
            [['email_address'], 'email'],
            [['employment_type'], 'in', 'range' => array_keys(self::getEmploymentTypeOptions())],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::DEFAULT_STATUS_ACTIVE],
            [['tsc_number'], 'required', 'when' => static fn(self $model): bool => $model->employment_type === self::EMPLOYMENT_TYPE_TSC, 'whenClient' => "function (attribute, value) { return $('#teacher-employment_type').val() === 'TSC'; }"],
            [['phone_number'], 'unique'],
            [['alternate_phone_number'], 'unique'],
            [['email_address'], 'unique'],
            [['tsc_number'], 'unique'],
            [['staff_number'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'other_names' => 'Other Names',
            'phone_number' => 'Phone Number',
            'alternate_phone_number' => 'Alternate Phone Number',
            'email_address' => 'Email Address',
            'date_of_birth' => 'Date of Birth',
            'employment_type' => 'Employment Type',
            'tsc_number' => 'TSC Number',
            'staff_number' => 'Staff Number',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getEmploymentTypeOptions(): array
    {
        return [
            self::EMPLOYMENT_TYPE_TSC => 'TSC Employed Teacher',
            self::EMPLOYMENT_TYPE_BOM => 'Board of Management Teacher',
            self::EMPLOYMENT_TYPE_PRIVATE => 'Privately Employed Teacher',
            self::EMPLOYMENT_TYPE_NGO => 'NGO Sponsored Teacher',
        ];
    }

    public static function getStatusOptions(): array
    {
        $options = LookupValue::find()
            ->select(['name', 'code'])
            ->where([
                'category' => 'teacher_status',
                'status' => LookupValue::STATUS_ACTIVE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('code')
            ->column();

        return $options ?: [self::DEFAULT_STATUS_ACTIVE => 'Active'];
    }

    public function getEmploymentTypeLabel(): string
    {
        return self::getEmploymentTypeOptions()[(string) $this->employment_type] ?? (string) $this->employment_type;
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(string) $this->status] ?? (string) $this->status;
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
