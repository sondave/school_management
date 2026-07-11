<?php

namespace app\components;

use Dotenv\Dotenv;
use Yii;
use yii\base\Component;

class Sms extends Component
{
    public $apiKey;
    public $apikey;
    public $apiUrl;
    public $shortCode;
    public $partnerId;  
    public $passType;

    public function init()
    {
        parent::init();

        $this->hydrateFromParams();
    }


    private function hydrateFromParams(): void
    {

        // Backward compatibility for accidental lowercase config key.
        if ($this->apiKey === null && $this->apikey !== null) {
            $this->apiKey = $this->apikey;
        }

        $smsParams = Yii::$app->params['SMS'] ?? [];
        $this->apiKey = $smsParams['API_KEY'] ?? null;
        $this->apiUrl = $smsParams['API_URL'] ?? null;
        $this->shortCode = $smsParams['SHORT_CODE'] ?? null;
        $this->partnerId = $smsParams['PARTNER_ID'] ?? null;
        $this->passType = $smsParams['PASS_TYPE'] ?? 'Plain';
    }



    public function send($phone, $message, $passType = NULL)
    {
        // if ($this->apiKey === null || $this->apiUrl === null || $this->shortCode === null || $this->partnerId === null) {
        //     $this->reloadEnvAndRehydrate();
        // }

        if ($this->apiKey === null || $this->apiUrl === null || $this->shortCode === null || $this->partnerId === null) {
            Yii::error([
                'event' => 'sms.config.missing',
                'apiKey_set' => $this->apiKey !== null,
                'apiUrl_set' => $this->apiUrl !== null,
                'shortCode_set' => $this->shortCode !== null,
                'partnerId_set' => $this->partnerId !== null,
            ], 'sms');

            return [
                'success' => false,
                'messageid' => null,
                'message_id' => null,
                'response-code' => 'CONFIG_MISSING',
                'response_code' => 'CONFIG_MISSING',
                'response-description' => 'Required SMS configuration keys are missing.',
                'response_description' => 'Required SMS configuration keys are missing.',
                'http_code' => 0,
                'curl_error' => null,
                'request_payload' => null,
                'raw_response' => null,
                'decoded_response' => null,
            ];
        }

        $requestPayload = [
            'partnerID' => $this->partnerId,
            'apikey' => $this->apiKey,
            'mobile' => $phone,
            'message' => $message,
            'shortcode' => $this->shortCode,
            'pass_type' => $passType ?? $this->passType,
        ];

        $maskedPayload = $requestPayload;
        if (!empty($maskedPayload['apikey'])) {
            $maskedPayload['apikey'] = str_repeat('*', max(strlen((string) $maskedPayload['apikey']) - 4, 0)) . substr((string) $maskedPayload['apikey'], -4);
        }

        Yii::info([
            'event' => 'sms.request',
            'url' => $this->apiUrl,
            'payload' => $maskedPayload,
        ], 'sms');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestPayload));

        $rawResponse = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

        Yii::info([
            'event' => 'sms.response',
            'http_code' => $httpCode,
            'curl_error' => $curlError ?: null,
            'raw_response' => $rawResponse,
        ], 'sms');

        curl_close($curl);

        $decodedResponse = json_decode((string) $rawResponse, true);
        if (!is_array($decodedResponse)) {
            $decodedResponse = [];
        }

        $messageId = $decodedResponse['messageid']
            ?? $decodedResponse['message_id']
            ?? null;
        $responseCode = $decodedResponse['response-code']
            ?? $decodedResponse['response_code']
            ?? (string) $httpCode;
        $responseDescription = $decodedResponse['response-description']
            ?? $decodedResponse['response_description']
            ?? ($curlError ?: ($rawResponse ?: 'No response returned by SMS gateway.'));
        $success = $curlError === '' && $httpCode >= 200 && $httpCode < 300;

        if (array_key_exists('success', $decodedResponse)) {
            $success = (bool) $decodedResponse['success'];
        }

        return [
            'success' => $success,
            'messageid' => $messageId,
            'message_id' => $messageId,
            'response-code' => $responseCode,
            'response_code' => $responseCode,
            'response-description' => $responseDescription,
            'response_description' => $responseDescription,
            'http_code' => $httpCode,
            'curl_error' => $curlError ?: null,
            'request_payload' => $maskedPayload,
            'raw_response' => $rawResponse,
            'decoded_response' => $decodedResponse,
        ];
    }
}