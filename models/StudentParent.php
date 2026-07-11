<?php

declare(strict_types=1);

namespace app\models;

use app\models\settings\LookupValue;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_parent_students".
 *
 * @property int $parent_id
 * @property int $student_id
 * @property string $relationship
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class StudentParent extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'st_parent_students';
    }

    public static function primaryKey(): array
    {
        return ['parent_id', 'student_id'];
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
            [['parent_id', 'student_id', 'relationship'], 'required'],
            [['parent_id', 'student_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['relationship'], 'string', 'max' => 50],
            [['relationship'], 'trim'],
            [['parent_id', 'student_id'], 'unique', 'targetAttribute' => ['parent_id', 'student_id'], 'message' => 'This parent is already linked to the student.'],
            [['parent_id'], 'exist', 'targetClass' => Parents::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['student_id'], 'exist', 'targetClass' => \app\models\Student::class, 'targetAttribute' => ['student_id' => 'id']],
            [['relationship'], 'in', 'range' => array_keys(self::getRelationshipOptions())],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'parent_id' => 'Parent',
            'student_id' => 'Student',
            'relationship' => 'Relationship',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getRelationshipOptions(): array
    {
        return LookupValue::find()
            ->select(['name', 'code'])
            ->where(['category' => 'relationship', 'status' => LookupValue::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_ASC])
            ->indexBy('code')
            ->column();
    }

    public static function getParentOptions(): array
    {
        $rows = Parents::find()
            ->select(['id', 'first_name', 'other_names'])
            ->orderBy(['first_name' => SORT_ASC, 'other_names' => SORT_ASC])
            ->asArray()
            ->all();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = trim((string) $row['first_name'] . ' ' . (string) $row['other_names']);
        }

        return $options;
    }

    public function getRelationshipLabel(): string
    {
        return self::getRelationshipOptions()[$this->relationship] ?? $this->relationship;
    }

    public function getParent()
    {
        return $this->hasOne(Parents::class, ['id' => 'parent_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(\app\models\Student::class, ['id' => 'student_id']);
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
