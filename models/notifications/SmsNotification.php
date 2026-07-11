<?php

declare(strict_types=1);

namespace app\models\notifications;

use app\models\Parents;
use app\models\User;
use app\models\settings\Grade;
use app\models\settings\SmsTemplate;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sms_notifications".
 *
 * @property int $id
 * @property string $tracking_id
 * @property int|null $sms_template_id
 * @property int|null $parent_id
 * @property int|null $student_id
 * @property int|null $grade_id
 * @property string $recipient_type
 * @property string $phone_number
 * @property string $message
 * @property int $status
 * @property string|null $short_code
 * @property string|null $message_id
 * @property string|null $network_id
 * @property string|null $response_code
 * @property string|null $response_description
 * @property string|null $sent_at
 * @property string|null $delivered_at
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class SmsNotification extends ActiveRecord
{
    public const STATUS_QUEUED = 0;
    public const STATUS_SUBMITTED = 1;
    public const STATUS_SENT = 2;
    public const STATUS_FAILED = 3;

    public static function tableName(): string
    {
        return 'sms_notifications';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['tracking_id', 'recipient_type', 'phone_number', 'message'], 'required'],
            [['sms_template_id', 'parent_id', 'student_id', 'grade_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['message', 'response_description'], 'string'],
            [['sent_at', 'delivered_at', 'created_at', 'updated_at'], 'safe'],
            [['tracking_id'], 'string', 'max' => 64],
            [['recipient_type'], 'string', 'max' => 30],
            [['phone_number'], 'string', 'max' => 20],
            [['short_code', 'message_id', 'network_id', 'response_code'], 'string', 'max' => 100],
            [['status'], 'default', 'value' => self::STATUS_QUEUED],
            [['status'], 'in', 'range' => array_keys(self::getStatusOptions())],
            [['recipient_type'], 'in', 'range' => [
                'all_parents',
                'by_grade',
                'specific_parent',
            ]],
            [['sms_template_id'], 'exist', 'targetClass' => SmsTemplate::class, 'targetAttribute' => ['sms_template_id' => 'id'], 'skipOnEmpty' => true],
            [['parent_id'], 'exist', 'targetClass' => Parents::class, 'targetAttribute' => ['parent_id' => 'id'], 'skipOnEmpty' => true],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id'], 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'tracking_id' => 'Tracking ID',
            'sms_template_id' => 'Template',
            'parent_id' => 'Parent',
            'student_id' => 'Student',
            'grade_id' => 'Grade',
            'recipient_type' => 'Recipient Type',
            'phone_number' => 'Phone Number',
            'message' => 'Message',
            'status' => 'Status',
            'sent_at' => 'Submitted At',
            'delivered_at' => 'Delivered At',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_QUEUED => 'Queued',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_SENT => 'Sent',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
    }

    public function getTemplate()
    {
        return $this->hasOne(SmsTemplate::class, ['id' => 'sms_template_id']);
    }

    public function getParent()
    {
        return $this->hasOne(Parents::class, ['id' => 'parent_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
