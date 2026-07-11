<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teacher_subjects}}`.
 */
class m260711_000014_create_teacher_subjects_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%teacher_subjects}}', [
            'id' => $this->primaryKey(),
            'teacher_id' => $this->integer()->notNull(),
            'grade_id' => $this->integer()->notNull(),
            'academic_year_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-teacher_subjects-teacher_id', '{{%teacher_subjects}}', 'teacher_id');
        $this->createIndex('idx-teacher_subjects-grade_id', '{{%teacher_subjects}}', 'grade_id');
        $this->createIndex('idx-teacher_subjects-academic_year_id', '{{%teacher_subjects}}', 'academic_year_id');
        $this->createIndex('idx-teacher_subjects-subject_id', '{{%teacher_subjects}}', 'subject_id');
        $this->createIndex(
            'uq-teacher_subjects-assignment',
            '{{%teacher_subjects}}',
            ['teacher_id', 'grade_id', 'academic_year_id', 'start_date', 'subject_id'],
            true
        );

        $this->addForeignKey(
            'fk-teacher_subjects-teacher_id',
            '{{%teacher_subjects}}',
            'teacher_id',
            '{{%teachers}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-teacher_subjects-grade_id',
            '{{%teacher_subjects}}',
            'grade_id',
            '{{%st_grades}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-teacher_subjects-academic_year_id',
            '{{%teacher_subjects}}',
            'academic_year_id',
            '{{%st_academic_years}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-teacher_subjects-subject_id',
            '{{%teacher_subjects}}',
            'subject_id',
            '{{%st_subjects}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-teacher_subjects-subject_id', '{{%teacher_subjects}}');
        $this->dropForeignKey('fk-teacher_subjects-academic_year_id', '{{%teacher_subjects}}');
        $this->dropForeignKey('fk-teacher_subjects-grade_id', '{{%teacher_subjects}}');
        $this->dropForeignKey('fk-teacher_subjects-teacher_id', '{{%teacher_subjects}}');

        $this->dropIndex('uq-teacher_subjects-assignment', '{{%teacher_subjects}}');
        $this->dropIndex('idx-teacher_subjects-subject_id', '{{%teacher_subjects}}');
        $this->dropIndex('idx-teacher_subjects-academic_year_id', '{{%teacher_subjects}}');
        $this->dropIndex('idx-teacher_subjects-grade_id', '{{%teacher_subjects}}');
        $this->dropIndex('idx-teacher_subjects-teacher_id', '{{%teacher_subjects}}');

        $this->dropTable('{{%teacher_subjects}}');
    }
}