<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_subjects}}`.
 */
class m260713_000026_create_exam_subjects_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%exam_subjects}}', [
            'id' => $this->primaryKey(),
            'exam_grade_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-exam_subjects-exam_grade_id', '{{%exam_subjects}}', 'exam_grade_id');
        $this->createIndex('idx-exam_subjects-subject_id', '{{%exam_subjects}}', 'subject_id');
        $this->createIndex('uq-exam_subjects-exam_grade_subject', '{{%exam_subjects}}', ['exam_grade_id', 'subject_id'], true);

        $this->addForeignKey('fk-exam_subjects-exam_grade_id', '{{%exam_subjects}}', 'exam_grade_id', '{{%exam_grades}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-exam_subjects-subject_id', '{{%exam_subjects}}', 'subject_id', '{{%st_subjects}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-exam_subjects-subject_id', '{{%exam_subjects}}');
        $this->dropForeignKey('fk-exam_subjects-exam_grade_id', '{{%exam_subjects}}');

        $this->dropIndex('uq-exam_subjects-exam_grade_subject', '{{%exam_subjects}}');
        $this->dropIndex('idx-exam_subjects-subject_id', '{{%exam_subjects}}');
        $this->dropIndex('idx-exam_subjects-exam_grade_id', '{{%exam_subjects}}');

        $this->dropTable('{{%exam_subjects}}');
    }
}
