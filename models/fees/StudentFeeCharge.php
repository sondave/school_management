<?php

declare(strict_types=1);

namespace app\models\fees;

use app\models\Student;
use app\models\settings\FeesStructure;
use app\models\fees\FeePaymentAllocation;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "st_student_fee_charges".
 *
 * @property int $id
 * @property int $student_id
 * @property int $fee_structure_id
 * @property float $amount
 * @property float $discount
 * @property float $balance
 */
class StudentFeeCharge extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'st_student_fee_charges';
    }

    public function rules(): array
    {
        return [
            [['student_id', 'fee_structure_id', 'amount'], 'required'],
            [['student_id', 'fee_structure_id'], 'integer'],
            [['amount', 'discount', 'balance'], 'number'],
            [['discount'], 'default', 'value' => 0],
            [['student_id', 'fee_structure_id'], 'unique', 'targetAttribute' => ['student_id', 'fee_structure_id']],
            [['student_id'], 'exist', 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['fee_structure_id'], 'exist', 'targetClass' => FeesStructure::class, 'targetAttribute' => ['fee_structure_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student',
            'fee_structure_id' => 'Fee Structure',
            'amount' => 'Amount',
            'discount' => 'Discount/Paid',
            'balance' => 'Balance',
        ];
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getFeeStructure()
    {
        return $this->hasOne(FeesStructure::class, ['id' => 'fee_structure_id']);
    }

    public function getPaymentAllocations()
    {
        return $this->hasMany(FeePaymentAllocation::class, ['student_fee_charge_id' => 'id']);
    }
}
