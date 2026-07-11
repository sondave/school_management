<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_grade_subjects}}`.
 */
class m260711_000006_create_st_grade_subjects_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_grade_subjects}}', [
            'id' => $this->primaryKey(),
            'grade_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_grade_subjects-grade_id', '{{%st_grade_subjects}}', 'grade_id');
        $this->createIndex('idx-st_grade_subjects-subject_id', '{{%st_grade_subjects}}', 'subject_id');
        $this->createIndex('idx-st_grade_subjects-grade_subject_unique', '{{%st_grade_subjects}}', ['grade_id', 'subject_id'], true);

        $this->addForeignKey('fk-st_grade_subjects-grade_id', '{{%st_grade_subjects}}', 'grade_id', '{{%st_grades}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-st_grade_subjects-subject_id', '{{%st_grade_subjects}}', 'subject_id', '{{%st_subjects}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-st_grade_subjects-subject_id', '{{%st_grade_subjects}}');
        $this->dropForeignKey('fk-st_grade_subjects-grade_id', '{{%st_grade_subjects}}');
        $this->dropIndex('idx-st_grade_subjects-grade_subject_unique', '{{%st_grade_subjects}}');
        $this->dropIndex('idx-st_grade_subjects-subject_id', '{{%st_grade_subjects}}');
        $this->dropIndex('idx-st_grade_subjects-grade_id', '{{%st_grade_subjects}}');
        $this->dropTable('{{%st_grade_subjects}}');
    }
}
