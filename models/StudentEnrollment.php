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
 * This is the model class for table "st_student_enrollments".
 *
 * @property int $id
 * @property int $student_id
 * @property int $academic_year_id
 * @property int $grade_id
 * @property int $is_current
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class StudentEnrollment extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'st_student_enrollments';
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
            [['student_id', 'academic_year_id', 'grade_id'], 'required'],
            [['student_id', 'academic_year_id', 'grade_id', 'is_current', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['is_current'], 'default', 'value' => 1],
            [['is_current'], 'in', 'range' => [0, 1]],
            [['student_id'], 'exist', 'targetClass' => \app\models\Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id']],
            ['is_current', 'validateOnlyOneCurrent'],
        ];
    }

    public function validateOnlyOneCurrent(string $attribute): void
    {
        if ((int) $this->$attribute !== 1 || empty($this->student_id)) {
            return;
        }

        $exists = self::find()
            ->where(['student_id' => (int) $this->student_id, 'is_current' => 1])
            ->andWhere(['<>', 'id', (int) ($this->id ?? 0)])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'Only one current enrollment is allowed for a student.');
        }
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        if ((int) $this->is_current === 1) {
            self::updateAll(
                ['is_current' => 0],
                ['and', ['student_id' => (int) $this->student_id], ['<>', 'id', (int) $this->id], ['is_current' => 1]]
            );
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student',
            'academic_year_id' => 'Academic Year',
            'grade_id' => 'Grade',
            'is_current' => 'Is Current',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
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

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->grade ?? '-';
    }

    public function getStudent()
    {
        return $this->hasOne(\app\models\Student::class, ['id' => 'student_id']);
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
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
