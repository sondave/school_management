<?php

declare(strict_types=1);

namespace app\models\notifications;

use app\models\Parents;
use app\models\StudentParent;
use app\models\StudentEnrollment;
use app\models\settings\Grade;
use app\models\settings\SmsTemplate;
use yii\base\Model;
use yii\db\Query;

class SmsNotificationDispatchForm extends Model
{
    public const RECIPIENT_ALL_PARENTS = 'all_parents';
    public const RECIPIENT_BY_GRADE = 'by_grade';
    public const RECIPIENT_SPECIFIC_PARENT = 'specific_parent';

    public ?string $recipient_type = self::RECIPIENT_ALL_PARENTS;
    public ?int $grade_id = null;
    public ?int $parent_id = null;
    public ?int $sms_template_id = null;
    public ?string $message = null;

    public function setAttributes($values, $safeOnly = true): void
    {
        if (is_array($values)) {
            $values = $this->normalizeIntegerInputs($values, ['grade_id', 'parent_id', 'sms_template_id']);
        }

        parent::setAttributes($values, $safeOnly);
    }

    public function rules(): array
    {
        return [
            [['recipient_type', 'sms_template_id', 'message'], 'required'],
            [['grade_id', 'parent_id', 'sms_template_id'], 'integer'],
            [['message'], 'string', 'max' => 1000],
            [['recipient_type'], 'in', 'range' => array_keys(self::getRecipientTypeOptions())],
            [['grade_id'], 'required', 'when' => fn() => $this->recipient_type === self::RECIPIENT_BY_GRADE, 'whenClient' => "function(){ return $('#smsnotificationdispatchform-recipient_type').val()==='by_grade'; }"],
            [['parent_id'], 'required', 'when' => fn() => $this->recipient_type === self::RECIPIENT_SPECIFIC_PARENT, 'whenClient' => "function(){ return $('#smsnotificationdispatchform-recipient_type').val()==='specific_parent'; }"],
            [['sms_template_id'], 'exist', 'targetClass' => SmsTemplate::class, 'targetAttribute' => ['sms_template_id' => 'id']],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id'], 'skipOnEmpty' => true],
            [['parent_id'], 'exist', 'targetClass' => Parents::class, 'targetAttribute' => ['parent_id' => 'id'], 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'recipient_type' => 'Send To',
            'grade_id' => 'Grade',
            'parent_id' => 'Parent',
            'sms_template_id' => 'SMS Template',
            'message' => 'Message',
        ];
    }

    public static function getRecipientTypeOptions(): array
    {
        return [
            self::RECIPIENT_ALL_PARENTS => 'To All Parents',
            self::RECIPIENT_BY_GRADE => 'By Grade',
            self::RECIPIENT_SPECIFIC_PARENT => 'Specific Parent',
        ];
    }

    public static function getTemplateOptions(): array
    {
        $rows = SmsTemplate::find()
            ->select(['id', 'name'])
            ->where(['status' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = (string) $row['name'];
        }

        return $options;
    }

    public static function getParentOptions(): array
    {
        $rows = Parents::find()
            ->select(['id', 'first_name', 'other_names', 'phone_no'])
            ->where(['status' => Parents::STATUS_ACTIVE])
            ->andWhere(['not', ['phone_no' => null]])
            ->andWhere(['<>', 'phone_no', ''])
            ->orderBy(['first_name' => SORT_ASC, 'other_names' => SORT_ASC])
            ->asArray()
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = trim((string) $row['first_name'] . ' ' . (string) $row['other_names']) . ' (' . (string) $row['phone_no'] . ')';
        }

        return $options;
    }

    public static function getGradeOptionsWithStudents(): array
    {
        $rows = (new Query())
            ->select(['g.id', 'g.grade'])
            ->from(['g' => Grade::tableName()])
            ->innerJoin(['e' => StudentEnrollment::tableName()], 'e.grade_id = g.id AND e.is_current = 1')
            ->innerJoin(['sp' => StudentParent::tableName()], 'sp.student_id = e.student_id')
            ->innerJoin(['p' => Parents::tableName()], 'p.id = sp.parent_id')
            ->where(['p.status' => Parents::STATUS_ACTIVE])
            ->andWhere(['not', ['p.phone_no' => null]])
            ->andWhere(['<>', 'p.phone_no', ''])
            ->groupBy(['g.id', 'g.grade'])
            ->orderBy(['g.id' => SORT_ASC])
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = (string) $row['grade'];
        }

        return $options;
    }

    /**
     * @return array<int,array{parent_id:int,student_id:int|null,grade_id:int|null,phone_number:string}>
     */
    public function resolveRecipients(): array
    {
        if ($this->recipient_type === self::RECIPIENT_ALL_PARENTS) {
            $rows = Parents::find()
                ->select(['id', 'phone_no'])
                ->where(['status' => Parents::STATUS_ACTIVE])
                ->andWhere(['not', ['phone_no' => null]])
                ->andWhere(['<>', 'phone_no', ''])
                ->asArray()
                ->all();

            $recipients = [];
            foreach ($rows as $row) {
                $recipients[] = [
                    'parent_id' => (int) $row['id'],
                    'student_id' => null,
                    'grade_id' => null,
                    'phone_number' => (string) $row['phone_no'],
                ];
            }

            return $recipients;
        }

        if ($this->recipient_type === self::RECIPIENT_BY_GRADE) {
            $rows = (new Query())
                ->select([
                    'p.id AS parent_id',
                    'e.student_id',
                    'e.grade_id',
                    'p.phone_no',
                ])
                ->from(['sp' => StudentParent::tableName()])
                ->innerJoin(['p' => Parents::tableName()], 'p.id = sp.parent_id')
                ->innerJoin(['e' => StudentEnrollment::tableName()], 'e.student_id = sp.student_id AND e.is_current = 1')
                ->where(['e.grade_id' => (int) $this->grade_id])
                ->andWhere(['p.status' => Parents::STATUS_ACTIVE])
                ->andWhere(['not', ['p.phone_no' => null]])
                ->andWhere(['<>', 'p.phone_no', ''])
                ->groupBy(['p.id', 'e.grade_id', 'p.phone_no'])
                ->all();

            $recipients = [];
            foreach ($rows as $row) {
                $recipients[] = [
                    'parent_id' => (int) $row['parent_id'],
                    'student_id' => isset($row['student_id']) ? (int) $row['student_id'] : null,
                    'grade_id' => isset($row['grade_id']) ? (int) $row['grade_id'] : null,
                    'phone_number' => (string) $row['phone_no'],
                ];
            }

            return $recipients;
        }

        $parent = Parents::find()
            ->select(['id', 'phone_no'])
            ->where(['id' => (int) $this->parent_id, 'status' => Parents::STATUS_ACTIVE])
            ->andWhere(['not', ['phone_no' => null]])
            ->andWhere(['<>', 'phone_no', ''])
            ->asArray()
            ->one();

        if ($parent === null) {
            return [];
        }

        return [[
            'parent_id' => (int) $parent['id'],
            'student_id' => null,
            'grade_id' => null,
            'phone_number' => (string) $parent['phone_no'],
        ]];
    }

    /**
     * @param array<string,mixed> $values
     * @param array<int,string> $keys
     * @return array<string,mixed>
     */
    private function normalizeIntegerInputs(array $values, array $keys): array
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $values)) {
                continue;
            }

            $raw = $values[$key];
            if ($raw === '' || $raw === null) {
                $values[$key] = null;
                continue;
            }

            if (is_int($raw)) {
                continue;
            }

            if (is_string($raw) && ctype_digit($raw)) {
                $values[$key] = (int) $raw;
            }
        }

        return $values;
    }
}
