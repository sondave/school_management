<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\AcademicYear;
use app\models\settings\Grade;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "class_teachers".
 *
 * @property int $id
 * @property int $grade_id
 * @property int $teacher_id
 * @property int $academic_year_id
 * @property string $start_date
 * @property string|null $end_date
 * @property int $is_current
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class ClassTeacher extends ActiveRecord
{
    public const CURRENT_NO = 0;
    public const CURRENT_YES = 1;

    public static function tableName(): string
    {
        return 'class_teachers';
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
            [['grade_id', 'teacher_id', 'academic_year_id', 'start_date', 'is_current'], 'required'],
            [['grade_id', 'teacher_id', 'academic_year_id', 'is_current', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['is_current'], 'in', 'range' => array_keys(self::getIsCurrentOptions())],
            [['is_current'], 'default', 'value' => self::CURRENT_NO],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id']],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id']],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'type' => 'date', 'skipOnEmpty' => true, 'message' => 'End date must be after start date.'],
            ['start_date', 'validateStartDateWithinAcademicYear'],
        ];
    }

    public function validateStartDateWithinAcademicYear(string $attribute): void
    {
        if ($this->hasErrors('academic_year_id') || $this->hasErrors($attribute) || empty($this->academic_year_id) || empty($this->$attribute)) {
            return;
        }

        $academicYear = AcademicYear::findOne((int) $this->academic_year_id);
        if ($academicYear === null || empty($academicYear->start_date) || empty($academicYear->end_date)) {
            return;
        }

        if ($this->$attribute < $academicYear->start_date || $this->$attribute > $academicYear->end_date) {
            $this->addError(
                $attribute,
                sprintf(
                    '%s must be between %s and %s of the selected academic year.',
                    $this->getAttributeLabel($attribute),
                    $academicYear->start_date,
                    $academicYear->end_date
                )
            );
        }
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ((int) $this->is_current === self::CURRENT_YES) {
            self::updateAll(
                ['is_current' => self::CURRENT_NO],
                [
                    'and',
                    ['<>', 'id', $this->id],
                    ['grade_id' => $this->grade_id],
                    ['is_current' => self::CURRENT_YES],
                ]
            );
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'grade_id' => 'Grade',
            'teacher_id' => 'Teacher',
            'academic_year_id' => 'Academic Year',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_current' => 'Current',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getIsCurrentOptions(): array
    {
        return [
            self::CURRENT_YES => 'Current',
            self::CURRENT_NO => 'Not Current',
        ];
    }

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->where(['status' => Grade::STATUS_ACTIVE])
            ->orderBy(['grade' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getTeacherOptions(): array
    {
        $items = Teacher::find()
            ->where(['status' => Teacher::DEFAULT_STATUS_ACTIVE])
            ->orderBy(['first_name' => SORT_ASC, 'other_names' => SORT_ASC])
            ->all();

        $options = [];
        foreach ($items as $item) {
            $options[(int) $item->id] = trim($item->first_name . ' ' . $item->other_names);
        }

        return $options;
    }

    public static function getAcademicYearOptions(): array
    {
        return AcademicYear::find()
            ->select(['year', 'id'])
            ->orderBy(['year' => SORT_DESC])
            ->indexBy('id')
            ->column();
    }

    public static function getAcademicYearDateRanges(): array
    {
        $rows = AcademicYear::find()
            ->select(['id', 'start_date', 'end_date'])
            ->asArray()
            ->all();

        $ranges = [];
        foreach ($rows as $row) {
            $ranges[(string) $row['id']] = [
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
            ];
        }

        return $ranges;
    }

    public function getIsCurrentLabel(): string
    {
        return self::getIsCurrentOptions()[(int) $this->is_current] ?? 'Unknown';
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->grade ?? 'Unknown Grade';
    }

    public function getTeacherLabel(): string
    {
        return trim(($this->teacher?->first_name ?? '') . ' ' . ($this->teacher?->other_names ?? '')) ?: '-';
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
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
