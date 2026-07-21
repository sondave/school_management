<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%st_students}}`, `{{%st_parent_students}}` and `{{%st_student_enrollments}}`.
 */
class m260711_000017_create_students_module_tables extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_students}}', [
            'id' => $this->primaryKey(),
            'upi' => $this->string(30)->null(),
            'access_number' => $this->string(30)->null(),
            'first_name' => $this->string(100)->notNull(),
            'middle_name' => $this->string(100)->notNull(),
            'surname' => $this->string(100)->notNull(),
            'gender_id' => $this->integer()->null(),
            'date_of_birth' => $this->date()->notNull(),
            'birth_cert_no' => $this->string(30)->null(),
            'admission_date' => $this->date()->null(),
            'admission_type' => $this->string(30)->notNull()->defaultValue('new_admission'),
            'transfered_from' => $this->string(100)->null(),
            'transfered_to' => $this->string(100)->null(),
            'has_special_needs' => $this->boolean()->notNull()->defaultValue(false),
            'status' => $this->integer()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('uq-st_students-upi', '{{%st_students}}', 'upi', true);
        $this->createIndex('uq-st_students-access_number', '{{%st_students}}', 'access_number', true);
        $this->createIndex('idx-st_students-gender_id', '{{%st_students}}', 'gender_id');
        $this->createIndex('idx-st_students-status', '{{%st_students}}', 'status');

        $this->addForeignKey(
            'fk-st_students-gender_id',
            '{{%st_students}}',
            'gender_id',
            '{{%st_lookup_values}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-st_students-status',
            '{{%st_students}}',
            'status',
            '{{%st_lookup_values}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('{{%st_parent_students}}', [
            'parent_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'relationship' => $this->string(50)->notNull(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
            'PRIMARY KEY(parent_id, student_id)',
        ]);

        $this->createIndex('idx-st_parent_students-student_id', '{{%st_parent_students}}', 'student_id');
        $this->createIndex('idx-st_parent_students-relationship', '{{%st_parent_students}}', 'relationship');

        $this->addForeignKey(
            'fk-st_parent_students-parent_id',
            '{{%st_parent_students}}',
            'parent_id',
            '{{%st_parents}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-st_parent_students-student_id',
            '{{%st_parent_students}}',
            'student_id',
            '{{%st_students}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%st_student_enrollments}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'academic_year_id' => $this->integer()->notNull(),
            'term_id' => $this->integer()->notNull(),
            'grade_id' => $this->integer()->notNull(),
            
            'is_current' => $this->boolean()->notNull()->defaultValue(true),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_student_enrollments-student_id', '{{%st_student_enrollments}}', 'student_id');
        $this->createIndex('idx-st_student_enrollments-academic_year_id', '{{%st_student_enrollments}}', 'academic_year_id');
        $this->createIndex('idx-st_student_enrollments-grade_id', '{{%st_student_enrollments}}', 'grade_id');
        $this->createIndex('idx-st_student_enrollments-term_id', '{{%st_student_enrollments}}', 'term_id');
        $this->createIndex('idx-st_student_enrollments-is_current', '{{%st_student_enrollments}}', 'is_current');

        $this->addForeignKey(
            'fk-st_student_enrollments-student_id',
            '{{%st_student_enrollments}}',
            'student_id',
            '{{%st_students}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-st_student_enrollments-academic_year_id',
            '{{%st_student_enrollments}}',
            'academic_year_id',
            '{{%st_academic_years}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-st_student_enrollments-grade_id',
            '{{%st_student_enrollments}}',
            'grade_id',
            '{{%st_grades}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-st_student_enrollments-term_id',
            '{{%st_student_enrollments}}',
            'term_id',
            '{{%st_terms}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-st_student_enrollments-term_id', '{{%st_student_enrollments}}');
        $this->dropForeignKey('fk-st_student_enrollments-grade_id', '{{%st_student_enrollments}}');
        $this->dropForeignKey('fk-st_student_enrollments-academic_year_id', '{{%st_student_enrollments}}');
        $this->dropForeignKey('fk-st_student_enrollments-student_id', '{{%st_student_enrollments}}');
        $this->dropIndex('idx-st_student_enrollments-is_current', '{{%st_student_enrollments}}');
        $this->dropIndex('idx-st_student_enrollments-grade_id', '{{%st_student_enrollments}}');
        $this->dropIndex('idx-st_student_enrollments-academic_year_id', '{{%st_student_enrollments}}');
        $this->dropIndex('idx-st_student_enrollments-student_id', '{{%st_student_enrollments}}');
        $this->dropTable('{{%st_student_enrollments}}');

        $this->dropForeignKey('fk-st_parent_students-student_id', '{{%st_parent_students}}');
        $this->dropForeignKey('fk-st_parent_students-parent_id', '{{%st_parent_students}}');
        $this->dropIndex('idx-st_parent_students-relationship', '{{%st_parent_students}}');
        $this->dropIndex('idx-st_parent_students-student_id', '{{%st_parent_students}}');
        $this->dropTable('{{%st_parent_students}}');

        $this->dropForeignKey('fk-st_students-status', '{{%st_students}}');
        $this->dropForeignKey('fk-st_students-gender_id', '{{%st_students}}');
        $this->dropIndex('idx-st_students-status', '{{%st_students}}');
        $this->dropIndex('idx-st_students-gender_id', '{{%st_students}}');
        $this->dropIndex('uq-st_students-access_number', '{{%st_students}}');
        $this->dropIndex('uq-st_students-upi', '{{%st_students}}');
        $this->dropTable('{{%st_students}}');
    }
}
