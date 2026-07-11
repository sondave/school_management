<?php

namespace app\components;

class EnvCrypto
{
    private static $cipher = 'aes-256-gcm';

    /**
     * Decrypts an encrypted configuration string using the master environment key
     */
    public static function decrypt($encryptedValue)
    {
        if (empty($encryptedValue)) {
            return null;
        }

        $masterKey = $_ENV['APP_ENCRYPTION_KEY'] ?? null;
        if (!$masterKey) {
            throw new \RuntimeException("Master encryption key (APP_ENCRYPTION_KEY) is missing from your environment variables.");
        }

        try {
            // Decode the payload container packet
            $payload = json_decode(base64_decode($encryptedValue), true);
            if (!isset($payload['iv'], $payload['value'], $payload['tag'])) {
                return $encryptedValue; // Fallback to plain text if string format doesn't match
            }

            $decrypted = openssl_decrypt(
                $payload['value'],
                self::$cipher,
                hex2bin($masterKey), // Convert hex master key back to binary
                0,
                base64_decode($payload['iv']),
                base64_decode($payload['tag'])
            );

            return $decrypted !== false ? $decrypted : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}