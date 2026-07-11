<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\AcademicYear;
use app\models\settings\Grade;
use app\models\settings\GradeSubject;
use app\models\settings\Subject;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "teacher_subjects".
 *
 * @property int $id
 * @property int $teacher_id
 * @property int $grade_id
 * @property int $academic_year_id
 * @property int|null $subject_id
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class TeacherSubject extends ActiveRecord
{
    public array $subject_ids = [];

    public array $groupRowIds = [];

    public static function tableName(): string
    {
        return 'teacher_subjects';
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
            [['teacher_id', 'grade_id', 'academic_year_id', 'start_date'], 'required'],
            [['teacher_id', 'grade_id', 'academic_year_id', 'subject_id', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['subject_ids'], 'required'],
            [['subject_ids'], 'each', 'rule' => ['integer']],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id']],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id']],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['subject_id'], 'exist', 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id'], 'skipOnEmpty' => true],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'type' => 'date', 'skipOnEmpty' => true, 'message' => 'End date must be after start date.'],
            ['start_date', 'validateStartDateWithinAcademicYear'],
            ['subject_ids', 'validateSubjectsForGrade'],
            ['subject_ids', 'validateUniqueAssignments'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher',
            'grade_id' => 'Grade',
            'academic_year_id' => 'Academic Year',
            'subject_id' => 'Subject',
            'subject_ids' => 'Subjects',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
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

    public function validateSubjectsForGrade(string $attribute): void
    {
        if ($this->hasErrors('grade_id') || empty($this->grade_id) || empty($this->$attribute)) {
            return;
        }

        $selectedIds = $this->getNormalizedSubjectIds();
        $allowedIds = array_map('intval', array_keys(self::getSubjectOptionsByGrade((int) $this->grade_id)));
        $invalidIds = array_diff($selectedIds, $allowedIds);

        if ($invalidIds !== []) {
            $this->addError($attribute, 'One or more selected subjects are not configured for the selected grade.');
        }
    }

    public function validateUniqueAssignments(string $attribute): void
    {
        if (
            $this->hasErrors('teacher_id')
            || $this->hasErrors('grade_id')
            || $this->hasErrors('academic_year_id')
            || $this->hasErrors('start_date')
            || empty($this->teacher_id)
            || empty($this->grade_id)
            || empty($this->academic_year_id)
            || empty($this->start_date)
            || empty($this->$attribute)
        ) {
            return;
        }

        $query = self::find()
            ->where([
                'teacher_id' => (int) $this->teacher_id,
                'grade_id' => (int) $this->grade_id,
                'academic_year_id' => (int) $this->academic_year_id,
                'start_date' => $this->start_date,
            ])
            ->andWhere(['subject_id' => $this->getNormalizedSubjectIds()]);

        if ($this->groupRowIds !== []) {
            $query->andWhere(['not in', 'id', $this->groupRowIds]);
        }

        $duplicateSubjectIds = array_map('intval', $query->select('subject_id')->column());
        if ($duplicateSubjectIds === []) {
            return;
        }

        $duplicateNames = Subject::find()
            ->select('name')
            ->where(['id' => $duplicateSubjectIds])
            ->orderBy(['name' => SORT_ASC])
            ->column();

        $this->addError(
            $attribute,
            'These subjects are already assigned to the same teacher for the same grade, academic year, and start date: ' . implode(', ', $duplicateNames)
        );
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

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->where(['status' => Grade::STATUS_ACTIVE])
            ->orderBy(['grade' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getAcademicYearOptions(): array
    {
        return AcademicYear::find()
            ->select(['year', 'id'])
            ->orderBy(['year' => SORT_DESC])
            ->indexBy('id')
            ->column();
    }

    public static function getSubjectOptionsByGrade(int $gradeId): array
    {
        if ($gradeId <= 0) {
            return [];
        }

        return Subject::find()
            ->alias('subject')
            ->select(['subject.name', 'subject.id'])
            ->innerJoin(['grade_subject' => GradeSubject::tableName()], 'grade_subject.subject_id = subject.id')
            ->where([
                'grade_subject.grade_id' => $gradeId,
                'grade_subject.status' => GradeSubject::STATUS_ACTIVE,
                'subject.status' => Subject::STATUS_ACTIVE,
            ])
            ->orderBy(['subject.name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getTeacherLabel(): string
    {
        return trim(($this->teacher?->first_name ?? '') . ' ' . ($this->teacher?->other_names ?? '')) ?: '-';
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->grade ?? '-';
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getSubjectLabel(): string
    {
        return $this->subject?->name ?? '-';
    }

    public function getSelectedSubjectLabels(): array
    {
        $subjectIds = $this->getNormalizedSubjectIds();
        if ($subjectIds === []) {
            if ($this->subject !== null) {
                return [$this->subject->name];
            }

            return [];
        }

        return Subject::find()
            ->select('name')
            ->where(['id' => $subjectIds])
            ->orderBy(['name' => SORT_ASC])
            ->column();
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    private function getNormalizedSubjectIds(): array
    {
        $subjectIds = array_map('intval', $this->subject_ids);
        $subjectIds = array_filter($subjectIds, static fn(int $id): bool => $id > 0);
        $subjectIds = array_values(array_unique($subjectIds));
        sort($subjectIds);

        return $subjectIds;
    }
}