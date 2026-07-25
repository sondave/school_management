<?php

namespace app\services;

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
        return [
            'unreadCount' => 0,
            'items' => [],
        ];
    }

    public static function isSupportedType(string $type): bool
    {
        return in_array($type, self::SUPPORTED_TYPES, true);
    }

    public static function markNotificationTypeAsReadForUser(int $userId, string $type): void
    {
    }

    public static function markAllAsReadForUser(int $userId): void
    {
    }
}
