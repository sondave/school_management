<?php

declare(strict_types=1);

namespace app\models\settings;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "exam_grades".
 *
 * @property int $id
 * @property int $exam_id
 * @property int $grade_id
 */
class ExamGrade extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'exam_grades';
    }

    public function rules(): array
    {
        return [
            [['exam_id', 'grade_id'], 'required'],
            [['exam_id', 'grade_id'], 'integer'],
            [['exam_id', 'grade_id'], 'unique', 'targetAttribute' => ['exam_id', 'grade_id']],
            [['exam_id'], 'exist', 'targetClass' => Exam::class, 'targetAttribute' => ['exam_id' => 'id']],
            [['grade_id'], 'exist', 'targetClass' => Grade::class, 'targetAttribute' => ['grade_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'exam_id' => 'Exam',
            'grade_id' => 'Grade',
        ];
    }

    public function getExam()
    {
        return $this->hasOne(Exam::class, ['id' => 'exam_id']);
    }

    public function getGrade()
    {
        return $this->hasOne(Grade::class, ['id' => 'grade_id']);
    }
}
