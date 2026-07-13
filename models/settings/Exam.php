<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use app\models\settings\ExamGrade;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "exams".
 *
 * @property int $id
 * @property string|null $exam_no
 * @property string $name
 * @property int $academic_year_id
 * @property int $term_id
 * @property int $exam_type_id
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Exam extends ActiveRecord
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_COMPLETED = 'completed';

    public static function tableName(): string
    {
        return 'exams';
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
            [['name', 'academic_year_id', 'term_id', 'exam_type_id', 'start_date', 'end_date', 'status'], 'required'],
            [['academic_year_id', 'term_id', 'exam_type_id', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['exam_no', 'name'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
            [['exam_no', 'name'], 'trim'],
            [['exam_no'], 'unique'],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>=', 'type' => 'date', 'message' => 'End date must be on or after start date.'],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['term_id'], 'exist', 'targetClass' => Term::class, 'targetAttribute' => ['term_id' => 'id']],
            [['exam_type_id'], 'exist', 'targetClass' => ExamType::class, 'targetAttribute' => ['exam_type_id' => 'id']],
        ];
    }

    public function beforeValidate(): bool
    {
        if ($this->shouldRegenerateExamNo()) {
            $this->exam_no = $this->buildUniqueExamNo();
        }

        return parent::beforeValidate();
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'exam_no' => 'Exam No',
            'name' => 'Name',
            'academic_year_id' => 'Academic Year',
            'term_id' => 'Term',
            'exam_type_id' => 'Exam Type',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
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
            self::STATUS_CANCELED => 'Canceled',
            self::STATUS_COMPLETED => 'Completed',
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

    public static function getTermOptions(?int $academicYearId = null): array
    {
        $query = Term::find()
            ->select(['name', 'id'])
            ->orderBy(['id' => SORT_ASC]);

        if ($academicYearId !== null && $academicYearId > 0) {
            $query->where(['academic_year_id' => $academicYearId]);
        }

        return $query
            ->indexBy('id')
            ->column();
    }

    public static function getExamTypeOptions(): array
    {
        return ExamType::find()
            ->select(['name', 'id'])
            ->where(['status' => ExamType::STATUS_ACTIVE])
            ->orderBy(['name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(string) $this->status] ?? 'Unknown';
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getTermLabel(): string
    {
        return $this->term?->name ?? '-';
    }

    public function getExamTypeLabel(): string
    {
        return $this->examType?->name ?? '-';
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
    }

    public function getTerm()
    {
        return $this->hasOne(Term::class, ['id' => 'term_id']);
    }

    public function getExamType()
    {
        return $this->hasOne(ExamType::class, ['id' => 'exam_type_id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getExamGrades()
    {
        return $this->hasMany(ExamGrade::class, ['exam_id' => 'id']);
    }

    public function getGrades()
    {
        return $this->hasMany(Grade::class, ['id' => 'grade_id'])->via('examGrades');
    }

    private function shouldRegenerateExamNo(): bool
    {
        if ($this->isNewRecord) {
            return true;
        }

        return $this->isAttributeChanged('academic_year_id')
            || $this->isAttributeChanged('term_id')
            || $this->isAttributeChanged('exam_type_id');
    }

    private function buildUniqueExamNo(): ?string
    {
        if (empty($this->academic_year_id) || empty($this->term_id) || empty($this->exam_type_id)) {
            return null;
        }

        $year = (string) AcademicYear::find()
            ->select('year')
            ->where(['id' => (int) $this->academic_year_id])
            ->scalar();

        $termName = (string) Term::find()
            ->select('name')
            ->where(['id' => (int) $this->term_id])
            ->scalar();

        $examTypeCode = (string) ExamType::find()
            ->select('code')
            ->where(['id' => (int) $this->exam_type_id])
            ->scalar();

        if ($year === '' || $termName === '' || $examTypeCode === '') {
            return null;
        }

        $termToken = $this->buildTermToken($termName);
        $typeToken = strtolower((string) preg_replace('/[^a-z0-9]/i', '', $examTypeCode));
        $prefix = strtolower($year . '-' . $termToken . '-' . $typeToken);

        $counter = $this->nextCounterForPrefix($prefix);
        for ($attempt = 0; $attempt < 500; ++$attempt) {
            $candidate = $prefix . $counter;

            $exists = self::find()
                ->where(['exam_no' => $candidate])
                ->andFilterWhere(['<>', 'id', (int) $this->id])
                ->exists();

            if (!$exists) {
                return $candidate;
            }

            ++$counter;
        }

        return $prefix . random_int(1000, 9999);
    }

    private function buildTermToken(string $termName): string
    {
        if (preg_match('/(\d+)/', $termName, $matches)) {
            return 't' . $matches[1];
        }

        return 't' . strtolower((string) preg_replace('/[^a-z0-9]/i', '', $termName));
    }

    private function nextCounterForPrefix(string $prefix): int
    {
        $examNumbers = self::find()
            ->select('exam_no')
            ->where(new Expression('exam_no LIKE :prefix', [':prefix' => $prefix . '%']))
            ->column();

        $maxCounter = 0;
        $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)$/i';
        foreach ($examNumbers as $examNo) {
            if (preg_match($pattern, (string) $examNo, $matches)) {
                $maxCounter = max($maxCounter, (int) $matches[1]);
            }
        }

        return $maxCounter + 1;
    }
}
