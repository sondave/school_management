<?php 

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use app\models\notifications\SmsNotification;

class SendSmsJob extends BaseObject implements JobInterface
{
    public $smsId;

    public function execute($queue)
    {
        $sms = SmsNotification::findOne($this->smsId);

        if (!$sms) {
            return;
        }

        // Idempotency guard: do not resend already delivered messages.
        if ((int) $sms->status === SmsNotification::STATUS_SENT) {
            return;
        }

        // Update status
        $sms->status = SmsNotification::STATUS_SUBMITTED;
        $sms->short_code = Yii::$app->sms->shortCode;
        $sms->sent_at = date('Y-m-d H:i:s');
        $sms->save(false);

        // send sms using the Sms component
        $smsComponent = Yii::$app->sms;
        $result = $smsComponent->send(
            $sms->phone_number,
            $sms->message
        );

        Yii::info([
            'event' => 'sms.job.result',
            'sms_id' => $sms->id,
            'tracking_id' => $sms->tracking_id,
            'request_payload' => $result['request_payload'] ?? null,
            'raw_response' => $result['raw_response'] ?? null,
            'decoded_response' => $result['decoded_response'] ?? null,
            'http_code' => $result['http_code'] ?? null,
            'curl_error' => $result['curl_error'] ?? null,
            'success' => $result['success'] ?? false,
        ], 'sms');
    
        if ($result['success']) {

            $sms->status = SmsNotification::STATUS_SENT;
            $sms->message_id = $result['messageid'] ?? $result['message_id'] ?? null;
            $sms->network_id = $result['networkid'] ?? $result['network_id'] ?? null;
            $sms->response_code = $result['response-code'] ?? $result['response_code'] ?? null;
            $sms->response_description = $result['response-description'] ?? $result['response_description'] ?? null;
            $sms->delivered_at = date('Y-m-d H:i:s');

        } else {

            $sms->status = SmsNotification::STATUS_FAILED;
            $sms->response_code = $result['response-code'] ?? $result['response_code'] ?? 'FAILED';
            $sms->response_description = $result['response-description'] ?? $result['response_description'] ?? 'SMS gateway request failed.';
            // $sms->retry_count++;
            // $sms->last_retry_at = date('Y-m-d H:i:s');
        }

        $sms->save(false);
    }
}