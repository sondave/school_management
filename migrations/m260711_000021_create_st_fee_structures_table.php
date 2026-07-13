<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_fee_structures}}`.
 */
class m260711_000021_create_st_fee_structures_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_fee_structures}}', [
            'id' => $this->primaryKey(),
            'academic_year_id' => $this->integer()->notNull(),
            'term_id' => $this->integer()->notNull(),
            'grade_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(12, 2)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_st_fee_structures_academic_year_id', '{{%st_fee_structures}}', ['academic_year_id']);
        $this->createIndex('idx_st_fee_structures_term_id', '{{%st_fee_structures}}', ['term_id']);
        $this->createIndex('idx_st_fee_structures_grade_id', '{{%st_fee_structures}}', ['grade_id']);
        $this->createIndex('idx_st_fee_structures_category_id', '{{%st_fee_structures}}', ['category_id']);

        $this->addForeignKey('fk_st_fee_structures_academic_year_id', '{{%st_fee_structures}}', 'academic_year_id', '{{%st_academic_years}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_st_fee_structures_term_id', '{{%st_fee_structures}}', 'term_id', '{{%st_terms}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_st_fee_structures_grade_id', '{{%st_fee_structures}}', 'grade_id', '{{%st_grades}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_st_fee_structures_category_id', '{{%st_fee_structures}}', 'category_id', '{{%st_fee_categories}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%st_student_fee_charges}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->notNull(),
            'fee_structure_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(12, 2)->notNull(),
            'discount' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'balance' => 'DECIMAL(12,2) GENERATED ALWAYS AS (amount - discount) STORED',
        ]);

        $this->createIndex('idx_st_student_fee_charges_student_id', '{{%st_student_fee_charges}}', ['student_id']);
        $this->createIndex('idx_st_student_fee_charges_fee_structure_id', '{{%st_student_fee_charges}}', ['fee_structure_id']);
        $this->createIndex('uq_st_student_fee_charges_student_structure', '{{%st_student_fee_charges}}', ['student_id', 'fee_structure_id'], true);

        $this->addForeignKey('fk_st_student_fee_charges_student_id', '{{%st_student_fee_charges}}', 'student_id', '{{%st_students}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_st_student_fee_charges_fee_structure_id', '{{%st_student_fee_charges}}', 'fee_structure_id', '{{%st_fee_structures}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_st_student_fee_charges_fee_structure_id', '{{%st_student_fee_charges}}');
        $this->dropForeignKey('fk_st_student_fee_charges_student_id', '{{%st_student_fee_charges}}');
        $this->dropIndex('uq_st_student_fee_charges_student_structure', '{{%st_student_fee_charges}}');
        $this->dropIndex('idx_st_student_fee_charges_fee_structure_id', '{{%st_student_fee_charges}}');
        $this->dropIndex('idx_st_student_fee_charges_student_id', '{{%st_student_fee_charges}}');
        $this->dropTable('{{%st_student_fee_charges}}');

        $this->dropForeignKey('fk_st_fee_structures_category_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_grade_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_term_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_academic_year_id', '{{%st_fee_structures}}');

        $this->dropTable('{{%st_fee_structures}}');
    }
}
