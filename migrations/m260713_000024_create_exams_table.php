<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exams}}`.
 */
class m260713_000024_create_exams_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%exams}}', [
            'id' => $this->primaryKey(),
            'exam_no' => $this->string(50)->notNull(),
            'name' => $this->string(50)->notNull(),
            'academic_year_id' => $this->integer()->notNull(),
            'term_id' => $this->integer()->notNull(),
            'exam_type_id' => $this->integer()->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'status' => $this->string(20)->notNull()->defaultValue('active'),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-exams-exam_no', '{{%exams}}', 'exam_no', true);
        $this->createIndex('idx-exams-name', '{{%exams}}', 'name');
        $this->createIndex('idx-exams-academic_year_id', '{{%exams}}', 'academic_year_id');
        $this->createIndex('idx-exams-term_id', '{{%exams}}', 'term_id');
        $this->createIndex('idx-exams-exam_type_id', '{{%exams}}', 'exam_type_id');
        $this->createIndex('idx-exams-status', '{{%exams}}', 'status');

        $this->addForeignKey('fk-exams-academic_year_id', '{{%exams}}', 'academic_year_id', '{{%st_academic_years}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-exams-term_id', '{{%exams}}', 'term_id', '{{%st_terms}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-exams-exam_type_id', '{{%exams}}', 'exam_type_id', '{{%st_exam_types}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-exams-exam_type_id', '{{%exams}}');
        $this->dropForeignKey('fk-exams-term_id', '{{%exams}}');
        $this->dropForeignKey('fk-exams-academic_year_id', '{{%exams}}');

        $this->dropIndex('idx-exams-status', '{{%exams}}');
        $this->dropIndex('idx-exams-exam_type_id', '{{%exams}}');
        $this->dropIndex('idx-exams-term_id', '{{%exams}}');
        $this->dropIndex('idx-exams-academic_year_id', '{{%exams}}');
        $this->dropIndex('idx-exams-name', '{{%exams}}');
        $this->dropIndex('idx-exams-exam_no', '{{%exams}}');

        $this->dropTable('{{%exams}}');
    }
}
