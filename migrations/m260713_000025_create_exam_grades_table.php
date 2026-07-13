<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_grades}}`.
 */
class m260713_000025_create_exam_grades_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%exam_grades}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'grade_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-exam_grades-exam_id', '{{%exam_grades}}', 'exam_id');
        $this->createIndex('idx-exam_grades-grade_id', '{{%exam_grades}}', 'grade_id');
        $this->createIndex('uq-exam_grades-exam_grade', '{{%exam_grades}}', ['exam_id', 'grade_id'], true);

        $this->addForeignKey('fk-exam_grades-exam_id', '{{%exam_grades}}', 'exam_id', '{{%exams}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-exam_grades-grade_id', '{{%exam_grades}}', 'grade_id', '{{%st_grades}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-exam_grades-grade_id', '{{%exam_grades}}');
        $this->dropForeignKey('fk-exam_grades-exam_id', '{{%exam_grades}}');

        $this->dropIndex('uq-exam_grades-exam_grade', '{{%exam_grades}}');
        $this->dropIndex('idx-exam_grades-grade_id', '{{%exam_grades}}');
        $this->dropIndex('idx-exam_grades-exam_id', '{{%exam_grades}}');

        $this->dropTable('{{%exam_grades}}');
    }
}
