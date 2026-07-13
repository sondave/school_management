<?php

declare(strict_types=1);

namespace app\models\settings;

use app\services\StudentFeeChargeService;
use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_fee_structures".
 *
 * @property int $id
 * @property int $academic_year_id
 * @property int $term_id
 * @property int $grade_id
 * @property int $category_id
 * @property float $amount
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class FeesStructure extends ActiveRecord
{
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    public static function tableName(): string
    {
        return 'st_fee_structures';
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
            [['academic_year_id', 'term_id', 'grade_id', 'category_id', 'amount', 'status'], 'required'],
            [['academic_year_id', 'term_id', 'grade_id', 'category_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number', 'min' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['term_id'], 'exist', 'targetClass' => Term::class, 'targetAttribute' => ['term_id' => 'id']],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id']],
            [['category_id'], 'exist', 'targetClass' => FeesCategory::class, 'targetAttribute' => ['category_id' => 'id']],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'academic_year_id' => 'Academic Year',
            'term_id' => 'Term',
            'grade_id' => 'Grade',
            'category_id' => 'Category',
            'amount' => 'Amount',
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

    public static function getAcademicYearOptions(): array
    {
        return AcademicYear::find()
            ->select(['year', 'id'])
            ->orderBy(['year' => SORT_DESC])
            ->indexBy('id')
            ->column();
    }

    public static function getTermOptions(): array
    {
        return Term::find()
            ->select(['name', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            StudentFeeChargeService::createChargesForFeeStructure($this);
        }
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
    }

    public function getTerm()
    {
        return $this->hasOne(Term::class, ['id' => 'term_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(FeesCategory::class, ['id' => 'category_id']);
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getTermLabel(): string
    {
        return $this->term?->name ?? '-';
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->grade ?? '-';
    }

    public function getCategoryLabel(): string
    {
        return $this->category?->name ?? '-';
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
