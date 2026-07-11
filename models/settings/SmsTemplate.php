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
            
               
        ];
    }

    public function getPrimaryKeyLabel()
    {
        if (strpos($this->name, 'PARENTS_') === 0) {
            return 'Parents';
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
