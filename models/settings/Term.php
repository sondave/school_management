<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_terms".
 *
 * @property int $id
 * @property int $academic_year_id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property int $current
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Term extends ActiveRecord
{
    public const CURRENT_NO = 0;
    public const CURRENT_YES = 1;
    public const NAME_TERM_1 = 'Term 1';
    public const NAME_TERM_2 = 'Term 2';
    public const NAME_TERM_3 = 'Term 3';

    public static function tableName(): string
    {
        return 'st_terms';
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
            [['academic_year_id', 'name', 'start_date', 'end_date', 'current'], 'required'],
            [['academic_year_id', 'current', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['name'], 'in', 'range' => array_keys(self::getNameOptions())],
            [['current'], 'in', 'range' => array_keys(self::getCurrentOptions())],
            [['current'], 'default', 'value' => self::CURRENT_NO],
            [['academic_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'type' => 'date', 'message' => 'End date must be after start date.'],
            [['start_date', 'end_date'], 'validateWithinAcademicYear'],
        ];
    }

    public function validateWithinAcademicYear(string $attribute): void
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

        if ((int) $this->current === self::CURRENT_YES) {
            self::updateAll(
                ['current' => self::CURRENT_NO],
                ['and', ['<>', 'id', $this->id], ['current' => self::CURRENT_YES]]
            );
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'academic_year_id' => 'Academic Year',
            'name' => 'Name',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'current' => 'Current',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getCurrentOptions(): array
    {
        return [
            self::CURRENT_YES => 'Current',
            self::CURRENT_NO => 'Not Current',
        ];
    }

    public static function getNameOptions(): array
    {
        return [
            self::NAME_TERM_1 => self::NAME_TERM_1,
            self::NAME_TERM_2 => self::NAME_TERM_2,
            self::NAME_TERM_3 => self::NAME_TERM_3,
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

    public function getCurrentLabel(): string
    {
        return self::getCurrentOptions()[(int) $this->current] ?? 'Unknown';
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
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
