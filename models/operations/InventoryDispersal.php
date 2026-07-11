<?php

declare(strict_types=1);

namespace app\models\operations;

use app\models\Parents;
use app\models\Teacher;
use app\models\User;
use app\models\settings\AcademicYear;
use app\models\settings\Grade;
use app\models\settings\Term;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "inventory_dispersals".
 *
 * @property int $id
 * @property string $accesory_type
 * @property int $inventory_item_id
 * @property string $dispersed_to
 * @property int|null $teacher_id
 * @property int|null $grade_id
 * @property int|null $student_id
 * @property int $academic_year_id
 * @property int $term_id
 * @property string $dispersed_on
 * @property int $qty_dispersed
 * @property int $is_to_be_returned
 * @property string|null $returned_on
 * @property int $qty_returned
 * @property int $missplaced
 * @property string|null $remarks
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class InventoryDispersal extends ActiveRecord
{
    public const DISPERSED_TO_TEACHER = 'teacher';
    public const DISPERSED_TO_STUDENT = 'student';
    public const SCENARIO_RECEIVE_BACK = 'receiveBack';

    public static function tableName(): string
    {
        return 'inventory_dispersals';
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

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DEFAULT] = [
            'accesory_type',
            'inventory_item_id',
            'dispersed_to',
            'teacher_id',
            'grade_id',
            'student_id',
            'academic_year_id',
            'term_id',
            'dispersed_on',
            'qty_dispersed',
            'is_to_be_returned',
            'returned_on',
            'qty_returned',
            'remarks',
        ];
        $scenarios[self::SCENARIO_RECEIVE_BACK] = [
            'is_to_be_returned',
            'returned_on',
            'qty_returned',
            'remarks',
        ];

        return $scenarios;
    }

    public function rules(): array
    {
        return [
            [['accesory_type', 'inventory_item_id', 'dispersed_to', 'academic_year_id', 'term_id', 'dispersed_on', 'qty_dispersed'], 'required'],
            [['inventory_item_id', 'teacher_id', 'grade_id', 'student_id', 'academic_year_id', 'term_id', 'qty_dispersed', 'qty_returned', 'missplaced', 'created_by', 'updated_by'], 'integer'],
            [['remarks'], 'string'],
            [['dispersed_on', 'returned_on', 'created_at', 'updated_at'], 'safe'],
            [['accesory_type'], 'string', 'max' => 50],
            [['dispersed_to'], 'string', 'max' => 20],
            [['is_to_be_returned'], 'boolean'],
            [['qty_dispersed', 'qty_returned', 'missplaced'], 'default', 'value' => 0],
            [['qty_dispersed', 'qty_returned', 'missplaced'], 'integer', 'min' => 0],
            [['remarks'], 'trim'],
            [['accesory_type'], 'in', 'range' => array_keys(Inventory::getAccessoryTypeOptions())],
            [['dispersed_to'], 'in', 'range' => array_keys(self::getDispersedToOptions())],
            [['inventory_item_id'], 'exist', 'targetClass' => InventoryItem::class, 'targetAttribute' => ['inventory_item_id' => 'id']],
            [['teacher_id'], 'exist', 'targetClass' => Teacher::class, 'targetAttribute' => ['teacher_id' => 'id'], 'skipOnEmpty' => true],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id'], 'skipOnEmpty' => true],
            [['student_id'], 'exist', 'targetClass' => Parents::class, 'targetAttribute' => ['student_id' => 'id'], 'skipOnEmpty' => true],
            [['academic_year_id'], 'exist', 'targetClass' => AcademicYear::class, 'targetAttribute' => ['academic_year_id' => 'id']],
            [['term_id'], 'exist', 'targetClass' => Term::class, 'targetAttribute' => ['term_id' => 'id']],
            [['dispersed_on', 'returned_on'], 'date', 'format' => 'php:Y-m-d'],
            [['inventory_item_id'], 'validateInventoryItemMatchesAccessoryType'],
            [['teacher_id', 'grade_id', 'student_id'], 'validateDispersedTarget'],
            [['returned_on', 'qty_returned'], 'validateReturnFields'],
            [['qty_returned'], 'compare', 'compareAttribute' => 'qty_dispersed', 'operator' => '<=', 'type' => 'number', 'message' => 'Qty Returned cannot exceed Qty Dispersed.'],
        ];
    }

    public function beforeValidate(): bool
    {
        if ($this->scenario === self::SCENARIO_RECEIVE_BACK) {
            $this->is_to_be_returned = 1;
        }

        if ((int) $this->is_to_be_returned !== 1) {
            $this->returned_on = null;
            $this->qty_returned = 0;
        }

        if ($this->dispersed_to === self::DISPERSED_TO_TEACHER) {
            $this->grade_id = null;
            $this->student_id = null;
        }

        if ($this->dispersed_to === self::DISPERSED_TO_STUDENT) {
            $this->teacher_id = null;
        }

        $this->missplaced = max(0, (int) $this->qty_dispersed - (int) ($this->qty_returned ?? 0));

        return parent::beforeValidate();
    }

    public function validateInventoryItemMatchesAccessoryType(string $attribute): void
    {
        if ($this->hasErrors('accesory_type') || empty($this->accesory_type) || empty($this->$attribute)) {
            return;
        }

        $exists = InventoryItem::find()
            ->where([
                'id' => (int) $this->$attribute,
                'accesory_type' => $this->accesory_type,
            ])
            ->exists();

        if (!$exists) {
            $this->addError($attribute, 'Selected inventory item does not match the selected accessory type.');
        }
    }

    public function validateDispersedTarget(string $attribute): void
    {
        if ($this->hasErrors('dispersed_to') || empty($this->dispersed_to)) {
            return;
        }

        if ($this->dispersed_to === self::DISPERSED_TO_TEACHER) {
            if (empty($this->teacher_id)) {
                $this->addError('teacher_id', 'Teacher is required when dispersed to Teacher.');
            }
            return;
        }

        if ($this->dispersed_to === self::DISPERSED_TO_STUDENT) {
            if (empty($this->grade_id)) {
                $this->addError('grade_id', 'Grade is required when dispersed to Student.');
            }

            if (empty($this->student_id)) {
                $this->addError('student_id', 'Student is required when dispersed to Student.');
            }
        }
    }

    public function validateReturnFields(string $attribute): void
    {
        if ((int) $this->is_to_be_returned !== 1) {
            return;
        }

        if (empty($this->returned_on)) {
            $this->addError('returned_on', 'Returned On is required when item is to be returned.');
        }

        if ($this->qty_returned === null || $this->qty_returned === '') {
            $this->addError('qty_returned', 'Qty Returned is required when item is to be returned.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'accesory_type' => 'Accessory Type',
            'inventory_item_id' => 'Inventory Item',
            'dispersed_to' => 'Dispersed To',
            'teacher_id' => 'Teacher',
            'grade_id' => 'Grade',
            'student_id' => 'Student',
            'academic_year_id' => 'Academic Year',
            'term_id' => 'Term',
            'dispersed_on' => 'Dispersed On',
            'qty_dispersed' => 'Qty Dispersed',
            'is_to_be_returned' => 'Is To Be Returned',
            'returned_on' => 'Returned On',
            'qty_returned' => 'Qty Returned',
            'missplaced' => 'Missplaced',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getDispersedToOptions(): array
    {
        return [
            self::DISPERSED_TO_STUDENT => 'Student',
            self::DISPERSED_TO_TEACHER => 'Teacher',
        ];
    }

    public static function getAccessoryTypeOptions(): array
    {
        return Inventory::getAccessoryTypeOptions();
    }

    public static function getInventoryItemOptions(?string $accesoryType = null): array
    {
        return InventoryItem::getOptions($accesoryType);
    }

    public static function getTeacherOptions(): array
    {
        $rows = Teacher::find()
            ->select(['id', 'first_name', 'other_names'])
            ->orderBy(['first_name' => SORT_ASC, 'other_names' => SORT_ASC])
            ->asArray()
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = trim((string) $row['first_name'] . ' ' . (string) $row['other_names']);
        }

        return $options;
    }

    public static function getGradeOptions(): array
    {
        return Grade::find()
            ->select(['grade', 'id'])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public static function getStudentOptions(?int $gradeId = null): array
    {
        $query = Parents::find()
            ->select(['id', 'first_name', 'other_names'])
            ->orderBy(['first_name' => SORT_ASC, 'other_names' => SORT_ASC]);

        $rows = $query->asArray()->all();
        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = trim((string) $row['first_name'] . ' ' . (string) $row['other_names']);
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

    public static function getTermOptions(?int $academicYearId = null): array
    {
        $query = Term::find()->select(['name', 'id']);
        if ($academicYearId !== null && $academicYearId > 0) {
            $query->andWhere(['academic_year_id' => $academicYearId]);
        }

        return $query
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    public function getAccessoryTypeLabel(): string
    {
        return Inventory::getAccessoryTypeOptions()[$this->accesory_type] ?? $this->accesory_type;
    }

    public function getDispersedToLabel(): string
    {
        return self::getDispersedToOptions()[$this->dispersed_to] ?? $this->dispersed_to;
    }

    public function getInventoryItemLabel(): string
    {
        return $this->inventoryItem?->name ?? '-';
    }

    public function getTeacherLabel(): string
    {
        if ($this->teacher === null) {
            return '-';
        }

        return trim((string) $this->teacher->first_name . ' ' . (string) $this->teacher->other_names);
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->grade ?? '-';
    }

    public function getStudentLabel(): string
    {
        if ($this->student === null) {
            return '-';
        }

        return trim((string) $this->student->first_name . ' ' . (string) $this->student->other_names);
    }

    public function getAcademicYearLabel(): string
    {
        return $this->academicYear?->year ?? '-';
    }

    public function getTermLabel(): string
    {
        return $this->term?->name ?? '-';
    }

    public function getInventoryItem()
    {
        return $this->hasOne(InventoryItem::class, ['id' => 'inventory_item_id']);
    }

    public function getTeacher()
    {
        return $this->hasOne(Teacher::class, ['id' => 'teacher_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Parents::class, ['id' => 'student_id']);
    }

    public function getAcademicYear()
    {
        return $this->hasOne(AcademicYear::class, ['id' => 'academic_year_id']);
    }

    public function getTerm()
    {
        return $this->hasOne(Term::class, ['id' => 'term_id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);

        $newItemId = (int) $this->inventory_item_id;
        $newIssued = (int) ($this->qty_dispersed ?? 0);
        $newReturned = (int) ((int) $this->is_to_be_returned === 1 ? ($this->qty_returned ?? 0) : 0);

        if ($insert) {
            $this->adjustStock($newItemId, 'total_issued', $newIssued);
            $this->adjustStock($newItemId, 'total_returned', $newReturned);
            return;
        }

        $oldItemId = isset($changedAttributes['inventory_item_id'])
            ? (int) $changedAttributes['inventory_item_id']
            : $newItemId;

        $oldIssued = isset($changedAttributes['qty_dispersed'])
            ? (int) ($changedAttributes['qty_dispersed'] ?? 0)
            : $newIssued;

        $oldIsToBeReturned = isset($changedAttributes['is_to_be_returned'])
            ? (int) ($changedAttributes['is_to_be_returned'] ?? 0)
            : (int) $this->is_to_be_returned;
        $oldQtyReturned = isset($changedAttributes['qty_returned'])
            ? (int) ($changedAttributes['qty_returned'] ?? 0)
            : $newReturned;
        $oldReturned = $oldIsToBeReturned === 1 ? $oldQtyReturned : 0;

        if ($oldItemId !== $newItemId) {
            $this->adjustStock($oldItemId, 'total_issued', -$oldIssued);
            $this->adjustStock($oldItemId, 'total_returned', -$oldReturned);
            $this->adjustStock($newItemId, 'total_issued', $newIssued);
            $this->adjustStock($newItemId, 'total_returned', $newReturned);
            return;
        }

        $issuedDiff = $newIssued - $oldIssued;
        if ($issuedDiff !== 0) {
            $this->adjustStock($newItemId, 'total_issued', $issuedDiff);
        }

        $returnedDiff = $newReturned - $oldReturned;
        if ($returnedDiff !== 0) {
            $this->adjustStock($newItemId, 'total_returned', $returnedDiff);
        }
    }

    public function afterDelete(): void
    {
        parent::afterDelete();

        $itemId = (int) $this->inventory_item_id;
        $issued = (int) ($this->qty_dispersed ?? 0);
        $returned = (int) ((int) $this->is_to_be_returned === 1 ? ($this->qty_returned ?? 0) : 0);

        $this->adjustStock($itemId, 'total_issued', -$issued);
        $this->adjustStock($itemId, 'total_returned', -$returned);
    }

    private function adjustStock(int $inventoryItemId, string $field, int $delta): void
    {
        if ($inventoryItemId <= 0 || $delta === 0) {
            return;
        }

        $stockLevel = StockLevel::ensureForInventoryItem($inventoryItemId);
        $current = (int) ($stockLevel->$field ?? 0);
        $stockLevel->$field = max(0, $current + $delta);
        $stockLevel->save(false);
    }
}