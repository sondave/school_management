<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\LookupValue;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_students".
 *
 * @property int $id
 * @property string|null $upi
 * @property string|null $nemis_no
 * @property string $first_name
 * @property string $middle_name
 * @property string $surname
 * @property int|null $gender_id
 * @property string $date_of_birth
 * @property string|null $birth_cert_no
 * @property string|null $admission_date
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Student extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'st_students';
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
            [['first_name', 'middle_name', 'surname', 'date_of_birth'], 'required'],
            [['gender_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['date_of_birth', 'admission_date', 'created_at', 'updated_at'], 'safe'],
            [['upi', 'nemis_no', 'birth_cert_no'], 'string', 'max' => 30],
            [['first_name', 'middle_name', 'surname'], 'string', 'max' => 100],
            [['upi', 'nemis_no', 'birth_cert_no', 'first_name', 'middle_name', 'surname'], 'trim'],
            [['upi', 'nemis_no', 'birth_cert_no', 'gender_id', 'status', 'admission_date'], 'default', 'value' => null],
            [['date_of_birth', 'admission_date'], 'date', 'format' => 'php:Y-m-d'],
            [['upi'], 'unique', 'skipOnEmpty' => true],
            [['nemis_no'], 'unique', 'skipOnEmpty' => true],
            [['gender_id'], 'exist', 'targetClass' => LookupValue::class, 'targetAttribute' => ['gender_id' => 'id'], 'skipOnEmpty' => true],
            [['status'], 'exist', 'targetClass' => LookupValue::class, 'targetAttribute' => ['status' => 'id'], 'skipOnEmpty' => true],
            [['gender_id'], 'validateGenderLookup'],
            [['status'], 'validateStatusLookup'],
        ];
    }

    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && empty($this->status)) {
            $this->status = self::getDefaultStatusId();
        }

        return parent::beforeValidate();
    }

    public function validateGenderLookup(string $attribute): void
    {
        if (empty($this->$attribute)) {
            return;
        }

        $exists = LookupValue::find()
            ->where([
                'id' => (int) $this->$attribute,
                'category' => 'gender',
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, 'Selected gender is invalid.');
        }
    }

    public function validateStatusLookup(string $attribute): void
    {
        if (empty($this->$attribute)) {
            return;
        }

        $exists = LookupValue::find()
            ->where([
                'id' => (int) $this->$attribute,
                'category' => 'student_status',
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, 'Selected status is invalid.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'upi' => 'UPI',
            'nemis_no' => 'NEMIS No',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'surname' => 'Surname',
            'gender_id' => 'Gender',
            'date_of_birth' => 'Date of Birth',
            'birth_cert_no' => 'Birth Cert No',
            'admission_date' => 'Admission Date',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getGenderOptions(): array
    {
        return LookupValue::find()
            ->select(['name', 'id'])
            ->where([
                'category' => 'gender',
                'status' => LookupValue::STATUS_ACTIVE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getStatusOptions(): array
    {
        return LookupValue::find()
            ->select(['name', 'id'])
            ->where([
                'category' => 'student_status',
                'status' => LookupValue::STATUS_ACTIVE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getDefaultStatusId(): ?int
    {
        $row = LookupValue::find()
            ->select(['id'])
            ->where([
                'category' => 'student_status',
                'status' => LookupValue::STATUS_ACTIVE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->one();

        return $row === null ? null : (int) $row['id'];
    }

    public function getFullName(): string
    {
        return trim((string) $this->first_name . ' ' . (string) $this->middle_name . ' ' . (string) $this->surname);
    }

    public function getGenderLabel(): string
    {
        return $this->genderLookup?->name ?? '-';
    }

    public function getStatusLabel(): string
    {
        return $this->statusLookup?->name ?? '-';
    }

    public function getGenderLookup()
    {
        return $this->hasOne(LookupValue::class, ['id' => 'gender_id']);
    }

    public function getStatusLookup()
    {
        return $this->hasOne(LookupValue::class, ['id' => 'status']);
    }

    public function getStudentParents()
    {
        return $this->hasMany(StudentParent::class, ['student_id' => 'id']);
    }

    public function getParentsRecords()
    {
        return $this->hasMany(Parents::class, ['id' => 'parent_id'])->via('studentParents');
    }

    public function getEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class, ['student_id' => 'id'])
            ->orderBy(['is_current' => SORT_DESC, 'id' => SORT_DESC]);
    }

    public function getCurrentEnrollment()
    {
        return $this->hasOne(StudentEnrollment::class, ['student_id' => 'id'])
            ->andWhere(['is_current' => 1])
            ->orderBy(['id' => SORT_DESC]);
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
