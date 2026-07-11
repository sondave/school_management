<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%class_teachers}}`.
 */
class m260711_000013_create_class_teachers_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%class_teachers}}', [
            'id' => $this->primaryKey(),
            'grade_id' => $this->integer()->notNull(),
            'teacher_id' => $this->integer()->notNull(),
            'academic_year_id' => $this->integer()->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->null(),
            'is_current' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-class_teachers-grade_id', '{{%class_teachers}}', 'grade_id');
        $this->createIndex('idx-class_teachers-teacher_id', '{{%class_teachers}}', 'teacher_id');
        $this->createIndex('idx-class_teachers-academic_year_id', '{{%class_teachers}}', 'academic_year_id');

        $this->addForeignKey(
            'fk-class_teachers-grade_id',
            '{{%class_teachers}}',
            'grade_id',
            '{{%st_grades}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-class_teachers-teacher_id',
            '{{%class_teachers}}',
            'teacher_id',
            '{{%teachers}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-class_teachers-academic_year_id',
            '{{%class_teachers}}',
            'academic_year_id',
            '{{%st_academic_years}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-class_teachers-academic_year_id', '{{%class_teachers}}');
        $this->dropForeignKey('fk-class_teachers-teacher_id', '{{%class_teachers}}');
        $this->dropForeignKey('fk-class_teachers-grade_id', '{{%class_teachers}}');

        $this->dropIndex('idx-class_teachers-academic_year_id', '{{%class_teachers}}');
        $this->dropIndex('idx-class_teachers-teacher_id', '{{%class_teachers}}');
        $this->dropIndex('idx-class_teachers-grade_id', '{{%class_teachers}}');

        $this->dropTable('{{%class_teachers}}');
    }
}
