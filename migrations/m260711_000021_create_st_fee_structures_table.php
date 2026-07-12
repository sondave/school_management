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
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_st_fee_structures_category_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_grade_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_term_id', '{{%st_fee_structures}}');
        $this->dropForeignKey('fk_st_fee_structures_academic_year_id', '{{%st_fee_structures}}');

        $this->dropTable('{{%st_fee_structures}}');
    }
}
