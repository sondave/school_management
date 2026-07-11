<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\SchoolInfo;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_parents".
 *
 * @property int $id
 * @property string $first_name
 * @property string $other_names
 * @property string $gender
 * @property string|null $national_id
 * @property string|null $date_of_birth
 * @property string $phone_no
 * @property string|null $alternate_phone_no
 * @property string|null $email
 * @property string $county
 * @property string $physical_address
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Parents extends ActiveRecord
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'st_parents';
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
            [['first_name', 'other_names', 'gender', 'phone_no', 'county', 'physical_address', 'status'], 'required'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['date_of_birth', 'created_at', 'updated_at'], 'safe'],
            [['first_name', 'other_names'], 'string', 'max' => 150],
            [['gender'], 'string', 'max' => 20],
            [['national_id'], 'string', 'max' => 20],
            [['phone_no', 'alternate_phone_no'], 'string', 'max' => 20],
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['county'], 'string', 'max' => 100],
            [['physical_address'], 'string', 'max' => 255],
            [['gender'], 'in', 'range' => array_keys(self::getGenderOptions())],
            [['county'], 'in', 'range' => array_keys(SchoolInfo::getKenyaCountyOptions())],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'other_names' => 'Other Names',
            'gender' => 'Gender',
            'national_id' => 'National ID',
            'date_of_birth' => 'Date of Birth',
            'phone_no' => 'Phone No',
            'alternate_phone_no' => 'Alternate Phone No',
            'email' => 'Email',
            'county' => 'County',
            'physical_address' => 'Physical Address',
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

    public static function getGenderOptions(): array
    {
        return [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
    }

    public function getGenderLabel(): string
    {
        return self::getGenderOptions()[(string) $this->gender] ?? (string) $this->gender;
    }

    public function getCountyLabel(): string
    {
        return SchoolInfo::getKenyaCountyOptions()[(string) $this->county] ?? (string) $this->county;
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
