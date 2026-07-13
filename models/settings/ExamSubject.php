<?php

declare(strict_types=1);

namespace app\models\settings;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "exam_subjects".
 *
 * @property int $id
 * @property int $exam_grade_id
 * @property int $subject_id
 */
class ExamSubject extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'exam_subjects';
    }

    public function rules(): array
    {
        return [
            [['exam_grade_id', 'subject_id'], 'required'],
            [['exam_grade_id', 'subject_id'], 'integer'],
            [['exam_grade_id', 'subject_id'], 'unique', 'targetAttribute' => ['exam_grade_id', 'subject_id']],
            [['exam_grade_id'], 'exist', 'targetClass' => ExamGrade::class, 'targetAttribute' => ['exam_grade_id' => 'id']],
            [['subject_id'], 'exist', 'targetClass' => Subject::class, 'targetAttribute' => ['subject_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'exam_grade_id' => 'Exam Grade',
            'subject_id' => 'Subject',
        ];
    }

    public function getExamGrade()
    {
        return $this->hasOne(ExamGrade::class, ['id' => 'exam_grade_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::class, ['id' => 'subject_id']);
    }
}
