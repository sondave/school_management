<?php


return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'EncryptionKey' => getenv('EncryptionKey') ?? 'EncryptionKey',
    'paybill' => (getenv('MPESA_SHORT_CODE') ? getenv('MPESA_SHORT_CODE') : ($_ENV['MPESA_SHORT_CODE'] ?? 'fallback-short-code')),
    'mpesa' => [
        'MPESA_CONSUMER_KEY' => getenv('MPESA_CONSUMER_KEY') ? getenv('MPESA_CONSUMER_KEY') : ($_ENV['MPESA_CONSUMER_KEY'] ?? 'fallback-key'),
        'MPESA_CONSUMER_SECRET' => getenv('MPESA_CONSUMER_SECRET') ? getenv('MPESA_CONSUMER_SECRET') : ($_ENV['MPESA_CONSUMER_SECRET'] ?? 'fallback-secret'),
        'MPESA_SHORT_CODE' => (getenv('MPESA_SHORT_CODE') ? getenv('MPESA_SHORT_CODE') : ($_ENV['MPESA_SHORT_CODE'] ?? 'fallback-short-code')),
        'MPESA_PASSKEY' => getenv('MPESA_PASSKEY') ? getenv('MPESA_PASSKEY') : ($_ENV['MPESA_PASSKEY'] ?? 'fallback-passkey'),
        'ACCESS_TOKEN_URL' => getenv('ACCESS_TOKEN_URL') ? getenv('ACCESS_TOKEN_URL') : ($_ENV['ACCESS_TOKEN_URL'] ?? 'fallback-access-token-url'),
        'STK_PUSH_URL' => getenv('STK_PUSH_URL') ? getenv('STK_PUSH_URL') : ($_ENV['STK_PUSH_URL'] ?? 'fallback-stk-push-url'),
        'CALLBACK_URL' => getenv('CALLBACK_URL') ? getenv('CALLBACK_URL') : ($_ENV['CALLBACK_URL'] ?? 'fallback-callback-url'),
        'STK_QUERY_URL' => getenv('STK_QUERY_URL') ? getenv('STK_QUERY_URL') : ($_ENV['STK_QUERY_URL'] ?? 'fallback-stk-query-url'),
        'REGISTER_URL' => getenv('REGISTER_URL') ? getenv('REGISTER_URL') : ($_ENV['REGISTER_URL'] ?? 'fallback-register-url'),
        'CONFIRMATION_URL' => getenv('CONFIRMATION_URL') ? getenv('CONFIRMATION_URL') : ($_ENV['CONFIRMATION_URL'] ?? 'fallback-confirmation-url'),
        'VALIDATION_URL' => getenv('VALIDATION_URL') ? getenv('VALIDATION_URL') : ($_ENV['VALIDATION_URL'] ?? 'fallback-validation-url'),
        
    ],
    'SMS' => [
        'API_URL' => getenv('SMS_API_URL') ?? null,
        'API_KEY' => getenv('SMS_API_KEY') ?? null,
        'SHORT_CODE' => getenv('SMS_SHORT_CODE') ?? null,
        'PARTNER_ID' => getenv('SMS_PARTNER_ID') ?? null,
        'PASS_TYPE' => getenv('SMS_PASS_TYPE') ?? 'Plain',
    ],
];
