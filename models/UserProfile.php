<?php

namespace app\models;

use app\models\settings\Branch;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $other_names
 * @property string $gender
 * @property string $phone_number
 * @property string $email
 * @property string|null $dob
 * @property int|null $branch_id
 * @property int|null $role
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class UserProfile extends ActiveRecord
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
        return 'user_profile';
    }

    public function rules()
    {
        return [
            [['first_name', 'other_names', 'gender', 'phone_number', 'email'], 'required'],
            [['user_id', 'branch_id', 'role', 'created_by', 'updated_by'], 'integer'],
            [['dob', 'created_at', 'updated_at'], 'safe'],
            [['first_name', 'gender'], 'string', 'max' => 50],
            [['other_names'], 'string', 'max' => 100],
            [['phone_number'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['user_id','email','phone_number'], 'unique'],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::class, 'targetAttribute' => ['branch_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'first_name' => 'First Name',
            'other_names' => 'Other Names',
            'gender' => 'Gender',
            'phone_number' => 'Phone Number',
            'email' => 'Email',
            'dob' => 'Date of Birth',
            'branch_id' => 'Branch',
            'role' => 'Role',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBranch()
    {
        return $this->hasOne(Branch::class, ['id' => 'branch_id']);
    }
}
