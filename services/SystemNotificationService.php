<?php

namespace app\services;

use app\models\Customer;
use app\models\Lead;
use app\models\Loan;
use app\models\Payment;
use app\models\UserNotificationItemRead;
use app\models\UserNotificationState;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;

class SystemNotificationService
{
    public const TYPE_CUSTOMER_ACTIVATION = 'customer_activation';
    public const TYPE_PENDING_LOANS = 'pending_loans';
    public const TYPE_LOAN_REPAYMENTS = 'loan_repayments';
    public const TYPE_FULLY_PAID_LOANS = 'fully_paid_loans';
    public const TYPE_NEW_LEADS = 'new_leads';

    private const SUPPORTED_TYPES = [
        self::TYPE_CUSTOMER_ACTIVATION,
        self::TYPE_PENDING_LOANS,
        self::TYPE_LOAN_REPAYMENTS,
        self::TYPE_FULLY_PAID_LOANS,
        self::TYPE_NEW_LEADS,
    ];

    /**
     * Builds unread notification categories since the user's last read time.
     *
     * @return array{unreadCount:int,items:array<int,array<string,mixed>>}
     */
    public static function getUnreadSummaryForUser(int $userId): array
    {
        $globalLastReadAt = self::getLastReadAt($userId);
        $typeReadMap = self::getTypeReadMap($userId);
        $items = [];

        $newActiveCustomers = self::getNewActiveCustomersCount(
            self::resolveEffectiveLastReadAt($globalLastReadAt, $typeReadMap[self::TYPE_CUSTOMER_ACTIVATION] ?? null)
        );
        if ($newActiveCustomers > 0) {
            $items[] = [
                'type' => self::TYPE_CUSTOMER_ACTIVATION,
                'title' => 'Customer Registration Fees',
                'message' => sprintf(
                    '%d new customer%s have paid their registration fee.',
                    $newActiveCustomers,
                    $newActiveCustomers === 1 ? '' : 's'
                ),
            ];
        }

        $pendingLoans = self::getPendingLoansCount(
            self::resolveEffectiveLastReadAt($globalLastReadAt, $typeReadMap[self::TYPE_PENDING_LOANS] ?? null)
        );
        if ($pendingLoans > 0) {
            $items[] = [
                'type' => self::TYPE_PENDING_LOANS,
                'title' => 'Loan Approvals',
                'message' => sprintf(
                    '%d new loan%s are pending approval.',
                    $pendingLoans,
                    $pendingLoans === 1 ? '' : 's'
                ),
            ];
        }

        $repaymentStats = self::getLoanRepaymentStats(
            self::resolveEffectiveLastReadAt($globalLastReadAt, $typeReadMap[self::TYPE_LOAN_REPAYMENTS] ?? null)
        );
        if ((float) $repaymentStats['amount'] > 0 && (int) $repaymentStats['loanCount'] > 0) {
            $items[] = [
                'type' => self::TYPE_LOAN_REPAYMENTS,
                'title' => 'Loan Repayments',
                'message' => sprintf(
                    'We have received KES %s as repayment for %d loan%s.',
                    number_format((float) $repaymentStats['amount'], 2),
                    (int) $repaymentStats['loanCount'],
                    (int) $repaymentStats['loanCount'] === 1 ? '' : 's'
                ),
            ];
        }

        $fullyPaidLoans = self::getFullyPaidLoansCount(
            self::resolveEffectiveLastReadAt($globalLastReadAt, $typeReadMap[self::TYPE_FULLY_PAID_LOANS] ?? null)
        );
        if ($fullyPaidLoans > 0) {
            $items[] = [
                'type' => self::TYPE_FULLY_PAID_LOANS,
                'title' => 'Completed Loans',
                'message' => sprintf(
                    '%d loan%s have been repaid in full.',
                    $fullyPaidLoans,
                    $fullyPaidLoans === 1 ? '' : 's'
                ),
            ];
        }

        $newLeads = self::getNewLeadsCount(
            self::resolveEffectiveLastReadAt($globalLastReadAt, $typeReadMap[self::TYPE_NEW_LEADS] ?? null)
        );
        if ($newLeads > 0) {
            $items[] = [
                'type' => self::TYPE_NEW_LEADS,
                'title' => 'Leads',
                'message' => sprintf(
                    '%d lead%s have been added.',
                    $newLeads,
                    $newLeads === 1 ? '' : 's'
                ),
            ];
        }

        return [
            'unreadCount' => count($items),
            'items' => $items,
        ];
    }

    public static function isSupportedType(string $type): bool
    {
        return in_array($type, self::SUPPORTED_TYPES, true);
    }

    public static function markNotificationTypeAsReadForUser(int $userId, string $type): void
    {
        if (!self::isSupportedType($type)) {
            return;
        }

        $now = new Expression('NOW()');

        Yii::$app->db->createCommand()->upsert(
            UserNotificationItemRead::tableName(),
            [
                'user_id' => $userId,
                'notification_type' => $type,
                'last_read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'last_read_at' => $now,
                'updated_at' => $now,
            ]
        )->execute();
    }

    public static function markAllAsReadForUser(int $userId): void
    {
        $now = new Expression('NOW()');

        Yii::$app->db->createCommand()->upsert(
            UserNotificationState::tableName(),
            [
                'user_id' => $userId,
                'last_read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'last_read_at' => $now,
                'updated_at' => $now,
            ]
        )->execute();
    }

    private static function getLastReadAt(int $userId): ?string
    {
        return UserNotificationState::find()
            ->select('last_read_at')
            ->where(['user_id' => $userId])
            ->scalar();
    }

    /**
     * @return array<string,string>
     */
    private static function getTypeReadMap(int $userId): array
    {
        return UserNotificationItemRead::find()
            ->select(['last_read_at', 'notification_type'])
            ->where(['user_id' => $userId])
            ->indexBy('notification_type')
            ->column();
    }

    private static function resolveEffectiveLastReadAt(?string $globalLastReadAt, ?string $typeLastReadAt): ?string
    {
        if ($globalLastReadAt === null || $globalLastReadAt === '') {
            return $typeLastReadAt;
        }

        if ($typeLastReadAt === null || $typeLastReadAt === '') {
            return $globalLastReadAt;
        }

        return strcmp($typeLastReadAt, $globalLastReadAt) > 0
            ? $typeLastReadAt
            : $globalLastReadAt;
    }

    private static function getNewActiveCustomersCount(?string $lastReadAt): int
    {
        $query = Payment::find()
            ->alias('p')
            ->innerJoin(
                '{{%customer}} c',
                '(c.id = p.customer_id OR c.national_id = p.account_no)'
            )
            ->where([
                'p.payment_purpose' => Payment::PURPOSE_REGISTRATION_FEE,
                'p.status' => Payment::STATUS_CONFIRMED,
                'c.status' => Customer::STATUS_ACTIVE,
            ]);

        self::applyUnreadFilter($query, 'p.created_at', $lastReadAt);

        return (int) $query->count('DISTINCT c.id');
    }

    private static function getPendingLoansCount(?string $lastReadAt): int
    {
        $query = Loan::find()
            ->where(['status' => Loan::STATUS_PENDING_APPROVAL]);

        self::applyUnreadFilter($query, 'created_on', $lastReadAt);

        return (int) $query->count();
    }

    /**
     * @return array{loanCount:int,amount:float}
     */
    private static function getLoanRepaymentStats(?string $lastReadAt): array
    {
        $query = Payment::find()
            ->alias('p')
            ->where([
                'p.payment_purpose' => Payment::PURPOSE_LOAN_REPAYMENT,
                'p.status' => Payment::STATUS_CONFIRMED,
            ]);

        self::applyUnreadFilter($query, 'p.created_at', $lastReadAt);

        $row = $query
            ->select([
                'loan_count' => 'COUNT(DISTINCT p.account_no)',
                'total_amount' => 'COALESCE(SUM(p.amount), 0)',
            ])
            ->asArray()
            ->one();

        return [
            'loanCount' => (int) ($row['loan_count'] ?? 0),
            'amount' => (float) ($row['total_amount'] ?? 0),
        ];
    }

    private static function getFullyPaidLoansCount(?string $lastReadAt): int
    {
        $query = Loan::find()
            ->where(['status' => Loan::STATUS_FULLY_PAID]);

        self::applyUnreadFilter($query, 'updated_at', $lastReadAt);

        return (int) $query->count();
    }

    private static function getNewLeadsCount(?string $lastReadAt): int
    {
        $query = Lead::find();

        self::applyUnreadFilter($query, 'created_at', $lastReadAt);

        return (int) $query->count();
    }

    private static function applyUnreadFilter(ActiveQuery $query, string $column, ?string $lastReadAt): void
    {
        if ($lastReadAt !== null && $lastReadAt !== '') {
            $query->andWhere(['>', $column, $lastReadAt]);
        }
    }
}
