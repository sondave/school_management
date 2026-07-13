<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_marks}}`.
 */
class m260713_000027_create_exam_marks_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%exam_marks}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'exam_grade_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'marks' => $this->decimal(7, 2)->notNull(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-exam_marks-exam_id', '{{%exam_marks}}', 'exam_id');
        $this->createIndex('idx-exam_marks-exam_grade_id', '{{%exam_marks}}', 'exam_grade_id');
        $this->createIndex('idx-exam_marks-student_id', '{{%exam_marks}}', 'student_id');
        $this->createIndex('idx-exam_marks-subject_id', '{{%exam_marks}}', 'subject_id');
        $this->createIndex(
            'uq-exam_marks-unique_entry',
            '{{%exam_marks}}',
            ['exam_id', 'exam_grade_id', 'student_id', 'subject_id'],
            true
        );

        $this->addForeignKey('fk-exam_marks-exam_id', '{{%exam_marks}}', 'exam_id', '{{%exams}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-exam_marks-exam_grade_id', '{{%exam_marks}}', 'exam_grade_id', '{{%exam_grades}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-exam_marks-student_id', '{{%exam_marks}}', 'student_id', '{{%st_students}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-exam_marks-subject_id', '{{%exam_marks}}', 'subject_id', '{{%st_subjects}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-exam_marks-subject_id', '{{%exam_marks}}');
        $this->dropForeignKey('fk-exam_marks-student_id', '{{%exam_marks}}');
        $this->dropForeignKey('fk-exam_marks-exam_grade_id', '{{%exam_marks}}');
        $this->dropForeignKey('fk-exam_marks-exam_id', '{{%exam_marks}}');

        $this->dropIndex('uq-exam_marks-unique_entry', '{{%exam_marks}}');
        $this->dropIndex('idx-exam_marks-subject_id', '{{%exam_marks}}');
        $this->dropIndex('idx-exam_marks-student_id', '{{%exam_marks}}');
        $this->dropIndex('idx-exam_marks-exam_grade_id', '{{%exam_marks}}');
        $this->dropIndex('idx-exam_marks-exam_id', '{{%exam_marks}}');

        $this->dropTable('{{%exam_marks}}');
    }
}
