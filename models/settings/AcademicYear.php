<?php

declare(strict_types=1);

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_academic_years".
 *
 * @property int $id
 * @property string $year
 * @property string $start_date
 * @property string $end_date
 * @property int $current
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AcademicYear extends ActiveRecord
{
    public const CURRENT_NO = 0;
    public const CURRENT_YES = 1;

    public static function tableName(): string
    {
        return 'st_academic_years';
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
            [['year', 'start_date', 'end_date', 'current'], 'required'],
            [['current', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['year'], 'string', 'max' => 20],
            [['year'], 'in', 'range' => array_keys(self::getYearOptions())],
            [['year'], 'unique'],
            [['current'], 'in', 'range' => array_keys(self::getCurrentOptions())],
            [['current'], 'default', 'value' => self::CURRENT_NO],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            ['end_date', 'compare', 'compareAttribute' => 'start_date', 'operator' => '>', 'type' => 'date', 'message' => 'End date must be after start date.'],
            [['start_date', 'end_date'], 'validateWithinSelectedYear'],
        ];
    }

    public function validateWithinSelectedYear(string $attribute): void
    {
        if ($this->hasErrors('year') || $this->hasErrors($attribute) || empty($this->year) || empty($this->$attribute)) {
            return;
        }

        $year = (string) $this->year;
        $minDate = $year . '-01-01';
        $maxDate = $year . '-12-31';

        if ($this->$attribute < $minDate || $this->$attribute > $maxDate) {
            $this->addError(
                $attribute,
                sprintf(
                    '%s must be between %s and %s for the selected year.',
                    $this->getAttributeLabel($attribute),
                    $minDate,
                    $maxDate
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
            'year' => 'Year',
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

    public static function getYearOptions(int $pastYears = 10, int $futureYears = 10): array
    {
        $currentYear = (int) date('Y');
        $startYear = $currentYear - $pastYears;
        $endYear = $currentYear + $futureYears;

        $options = [];
        for ($year = $endYear; $year >= $startYear; --$year) {
            $value = (string) $year;
            $options[$value] = $value;
        }

        return $options;
    }

    public static function getYearDateRanges(): array
    {
        $ranges = [];
        foreach (array_keys(self::getYearOptions()) as $year) {
            $ranges[(string) $year] = [
                'start_date' => $year . '-01-01',
                'end_date' => $year . '-12-31',
            ];
        }

        return $ranges;
    }

    public function getCurrentLabel(): string
    {
        return self::getCurrentOptions()[(int) $this->current] ?? 'Unknown';
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
