<?php

declare(strict_types=1);

namespace app\models\fees;

use app\models\Student;
use app\models\User;
use app\models\fees\FeePaymentAllocation;
use app\models\settings\FeesStructure;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "fee_payments".
 *
 * @property int $id
 * @property string $receipt_no
 * @property int $student_id
 * @property string $payment_date
 * @property string $payment_method
 * @property string|null $remarks
 * @property float $amount
 * @property int|null $created_by
 * @property string|null $created_at
 */
class FeePayment extends ActiveRecord
{
    public const METHOD_CASH = 'Cash';
    public const METHOD_MPESA = 'M-Pesa';
    public const METHOD_BANK = 'Bank';
    public const METHOD_CHEQUE = 'Cheque';
    public const METHOD_OTHER = 'Other';

    public static function tableName(): string
    {
        return 'fee_payments';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['student_id', 'payment_date', 'payment_method'], 'required'],
            [['student_id', 'created_by'], 'integer'],
            [['payment_date', 'created_at'], 'safe'],
            [['remarks'], 'string'],
            [['amount'], 'number', 'min' => 0.01],
            [['receipt_no'], 'string', 'max' => 50],
            [['payment_method'], 'string', 'max' => 50],
            [['receipt_no'], 'unique'],
            [['payment_method'], 'in', 'range' => array_values(self::getPaymentMethodOptions())],
            [['payment_date'], 'date', 'format' => 'php:Y-m-d'],
            [['student_id'], 'exist', 'targetClass' => Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['created_by'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id'], 'skipOnEmpty' => true],
        ];
    }

    public function beforeValidate(): bool
    {
        if ($this->isNewRecord && empty($this->receipt_no)) {
            $this->receipt_no = $this->generateReceiptNo();
        }

        return parent::beforeValidate();
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'receipt_no' => 'Receipt No',
            'student_id' => 'Student',
            'payment_date' => 'Payment Date',
            'payment_method' => 'Payment Method',
            'remarks' => 'Remarks',
            'amount' => 'Amount',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    public static function getPaymentMethodOptions(): array
    {
        return [
            self::METHOD_CASH => self::METHOD_CASH,
            self::METHOD_MPESA => self::METHOD_MPESA,
            self::METHOD_BANK => self::METHOD_BANK,
            self::METHOD_CHEQUE => self::METHOD_CHEQUE,
            self::METHOD_OTHER => self::METHOD_OTHER,
        ];
    }

    public static function getStudentOptions(): array
    {
        $students = Student::find()
            ->orderBy(['first_name' => SORT_ASC, 'middle_name' => SORT_ASC, 'surname' => SORT_ASC])
            ->all();

        $options = [];
        foreach ($students as $student) {
            $options[(int) $student->id] = $student->getFullName();
        }

        return $options;
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['id' => 'student_id']);
    }

    public function getAllocations()
    {
        return $this->hasMany(FeePaymentAllocation::class, ['payment_id' => 'id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getAcademicYearLabel(): string
    {
        $feeStructure = $this->getFirstFeeStructure();
        return $feeStructure?->academicYear?->year ?? '-';
    }

    public function getTermLabel(): string
    {
        $feeStructure = $this->getFirstFeeStructure();
        return $feeStructure?->term?->name ?? '-';
    }

    public function getGradeLabel(): string
    {
        $feeStructure = $this->getFirstFeeStructure();
        return $feeStructure?->grade?->grade ?? '-';
    }

    private function getFirstFeeStructure(): ?FeesStructure
    {
        foreach ((array) $this->allocations as $allocation) {
            $feeStructure = $allocation->studentFeeCharge?->feeStructure;
            if ($feeStructure !== null) {
                return $feeStructure;
            }
        }

        return null;
    }

    private function generateReceiptNo(): string
    {
        $prefix = 'RCPT-' . date('Ymd');
        for ($i = 0; $i < 10; ++$i) {
            $candidate = sprintf('%s-%04d', $prefix, random_int(1, 9999));
            $exists = self::find()->where(['receipt_no' => $candidate])->exists();
            if (!$exists) {
                return $candidate;
            }
        }

        return $prefix . '-' . Yii::$app->security->generateRandomString(6);
    }
}
