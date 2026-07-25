<?php

namespace app\models\settings;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sms_template".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $template
 * @property int $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class SmsTemplate extends ActiveRecord
{
    public function behaviors()
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

    public static function tableName()
    {
        return 'sms_template';
    }

    // Define your fixed structural groups
    const GROUP_PARENTS = 'PARENTS';
    const GROUP_TEACHERS = 'TEACHERS';
    const GROUP_USERS = 'USERS';


    /**
     * Map out every single valid configuration key in the entire system,
     * assigning it to a group and giving it a human-readable label.
     */

    
    public static function getGroupDropdownList()
    {
        return [
            
            
            'Parents SMS Configurations' => [
                self::GROUP_PARENTS . '_SCHOOL_OPENING_DATE_ALERT' => 'School Opening Date Alert',
                self::GROUP_PARENTS . '_SCHOOL_CLOSING_DATE_ALERT' => 'School Closing Date Alert',
                self::GROUP_PARENTS . '_STUDENT_SCHOOL_FEES_AREARS_ALERT' => 'Student School Fees Arears Alert',

            ],

            'Users SMS Configurations' => [
                self::GROUP_USERS . '_USER_ACTIVATION_ALERT' => 'User Activation Alert',
                self::GROUP_USERS . '_USER_ACTIVATION_RESEND_ALERT' => 'User Activation Resend Alert',
                self::GROUP_USERS . '_USER_EXPIRED_ACTIVATION_ALERT' => 'User Expired Activation Alert',
                self::GROUP_USERS . '_USER_PASSWORD_RESET_ALERT' => 'User Password Reset Alert',
            ],
            
               
        ];
    }

    /**
     * @return array<string,string>
     */
    public static function getNameLabelMap(): array
    {
        $map = [];
        foreach (self::getGroupDropdownList() as $groupOptions) {
            foreach ($groupOptions as $value => $label) {
                $map[(string) $value] = (string) $label;
            }
        }

        return $map;
    }

    public static function resolveNameLabel(string $value): string
    {
        $map = self::getNameLabelMap();
        return $map[$value] ?? $value;
    }

    public function getNameLabel(): string
    {
        return self::resolveNameLabel((string) $this->name);
    }

    public function getPrimaryKeyLabel()
    {
        if (strpos($this->name, 'PARENTS_') === 0) {
            return 'Parents';
        }
        if (strpos($this->name, 'TEACHERS_') === 0) {
            return 'Teachers';
        }
        if (strpos($this->name, 'USERS_') === 0) {
            return 'Users';
        }

    }
    

    public function rules()
    {
        return [
            [['name', 'template', 'status'], 'required'],
            [['status'], 'boolean'],
            [['status'], 'default', 'value' => 1],
            [['description'], 'string', 'max' => 255],
            [['template'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'template' => 'Template',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
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
