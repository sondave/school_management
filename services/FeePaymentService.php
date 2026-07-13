<?php

declare(strict_types=1);

namespace app\services;

use app\models\fees\FeePayment;
use app\models\fees\FeePaymentAllocation;
use app\models\fees\StudentFeeCharge;
use Yii;
use yii\db\Expression;

class FeePaymentService
{
    /**
     * @param array<int, array<string, mixed>> $allocations
     */
    public static function createPayment(FeePayment $payment, array $allocations): bool
    {
        $requestedAllocations = self::extractSelectedAllocations($allocations);
        if (empty($requestedAllocations)) {
            $payment->addError('amount', 'Select at least one fee item and provide an amount.');
            return false;
        }

        $chargeIds = array_keys($requestedAllocations);
        $charges = StudentFeeCharge::find()
            ->with(['feeStructure.category'])
            ->where([
                'id' => $chargeIds,
                'student_id' => (int) $payment->student_id,
            ])
            ->indexBy('id')
            ->all();

        if (count($charges) !== count($chargeIds)) {
            $payment->addError('amount', 'Some selected fee items are invalid for this student.');
            return false;
        }

        $totalAmount = 0.0;
        foreach ($requestedAllocations as $chargeId => $amount) {
            $charge = $charges[$chargeId] ?? null;
            if ($charge === null) {
                $payment->addError('amount', 'Invalid fee allocation selected.');
                return false;
            }

            $balance = (float) $charge->balance;
            if ($amount > $balance) {
                $payment->addError('amount', sprintf(
                    'Allocated amount for %s cannot exceed balance %.2f.',
                    $charge->feeStructure?->category?->name ?? 'selected item',
                    $balance
                ));
                return false;
            }

            $totalAmount += $amount;
        }

        if ($totalAmount <= 0) {
            $payment->addError('amount', 'Payment amount must be greater than zero.');
            return false;
        }

        $payment->amount = $totalAmount;
        if (!$payment->validate()) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$payment->save(false)) {
                $transaction->rollBack();
                return false;
            }

            foreach ($requestedAllocations as $chargeId => $amount) {
                $allocation = new FeePaymentAllocation([
                    'payment_id' => (int) $payment->id,
                    'student_fee_charge_id' => $chargeId,
                    'amount' => $amount,
                ]);

                if (!$allocation->save()) {
                    $payment->addErrors($allocation->getErrors());
                    $transaction->rollBack();
                    return false;
                }

                Yii::$app->db->createCommand()->update(
                    StudentFeeCharge::tableName(),
                    ['discount' => new Expression('discount + :paid', [':paid' => $amount])],
                    ['id' => $chargeId]
                )->execute();
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            Yii::error([
                'event' => 'fee_payment_create_failed',
                'message' => $exception->getMessage(),
                'student_id' => (int) $payment->student_id,
                'payment_date' => (string) $payment->payment_date,
                'payment_method' => (string) $payment->payment_method,
                'computed_amount' => (float) $payment->amount,
                'allocations' => $requestedAllocations,
                'exception' => (string) $exception,
            ], __METHOD__);
            $payment->addError('amount', 'Unable to post payment at the moment. Please try again.');
            return false;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $allocations
     * @return array<int, float>
     */
    private static function extractSelectedAllocations(array $allocations): array
    {
        $selected = [];

        foreach ($allocations as $chargeId => $allocation) {
            $chargeId = (int) $chargeId;
            if ($chargeId <= 0) {
                continue;
            }

            $isSelected = isset($allocation['selected']) && (string) $allocation['selected'] === '1';
            $amount = isset($allocation['amount']) ? (float) $allocation['amount'] : 0.0;

            if (!$isSelected) {
                continue;
            }

            if ($amount <= 0) {
                continue;
            }

            $selected[$chargeId] = $amount;
        }

        return $selected;
    }
}
