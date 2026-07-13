<?php

declare(strict_types=1);

namespace app\models\fees;

use app\models\fees\FeePayment;
use app\models\fees\StudentFeeCharge;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "payment_allocations".
 *
 * @property int $id
 * @property int $payment_id
 * @property int $student_fee_charge_id
 * @property float $amount
 */
class FeePaymentAllocation extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'payment_allocations';
    }

    public function rules(): array
    {
        return [
            [['payment_id', 'student_fee_charge_id', 'amount'], 'required'],
            [['payment_id', 'student_fee_charge_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.01],
            [['payment_id'], 'exist', 'targetClass' => FeePayment::class, 'targetAttribute' => ['payment_id' => 'id']],
            [['student_fee_charge_id'], 'exist', 'targetClass' => StudentFeeCharge::class, 'targetAttribute' => ['student_fee_charge_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'payment_id' => 'Payment',
            'student_fee_charge_id' => 'Student Fee Charge',
            'amount' => 'Amount',
        ];
    }

    public function getPayment()
    {
        return $this->hasOne(FeePayment::class, ['id' => 'payment_id']);
    }

    public function getStudentFeeCharge()
    {
        return $this->hasOne(StudentFeeCharge::class, ['id' => 'student_fee_charge_id']);
    }
}
