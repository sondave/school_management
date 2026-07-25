<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $other_names
 * @property string $gender
 * @property string $phone
 * @property string $dob
 */
class UserProfile extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_profile';
    }

    public function rules()
    {
        return [
            [['first_name', 'other_names', 'gender', 'phone', 'dob'], 'required'],
            [['user_id'], 'integer'],
            [['dob'], 'safe'],
            [['first_name', 'gender'], 'string', 'max' => 50],
            [['other_names'], 'string', 'max' => 150],
            [['phone'], 'string', 'max' => 20],
            [['user_id'], 'unique'],
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
            'phone' => 'Phone Number',
            'dob' => 'Date of Birth',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
