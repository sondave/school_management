<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%inventory_items}}`, `{{%stock_levels}}`, `{{%inventoy}}` and `{{%inventory_dispersals}}`.
 */
class m260711_000016_create_inventoy_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%inventory_items}}', [
            'id' => $this->primaryKey(),
            'accesory_type' => $this->string(50)->notNull(),
            'name' => $this->string(190)->notNull(),
            'description' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-inventory_items-accesory_type', '{{%inventory_items}}', 'accesory_type');
        $this->createIndex('idx-inventory_items-name', '{{%inventory_items}}', 'name');
        $this->createIndex('uq-inventory_items-accesory_type-name', '{{%inventory_items}}', ['accesory_type', 'name'], true);

        $this->createTable('{{%stock_levels}}', [
            'id' => $this->primaryKey(),
            'inventory_item_id' => $this->integer()->notNull(),
            'total_received' => $this->integer()->notNull()->defaultValue(0),
            'total_issued' => $this->integer()->notNull()->defaultValue(0),
            'total_returned' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex('idx-stock_levels-inventory_item_id', '{{%stock_levels}}', 'inventory_item_id');
        $this->createIndex('uq-stock_levels-inventory_item_id', '{{%stock_levels}}', 'inventory_item_id', true);

        $this->addForeignKey(
            'fk-stock_levels-inventory_item_id',
            '{{%stock_levels}}',
            'inventory_item_id',
            '{{%inventory_items}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%inventoy}}', [
            'id' => $this->primaryKey(),
            'accesory_type' => $this->string(50)->notNull(),
            'inventory_item_id' => $this->integer()->notNull(),
            'supplier_id' => $this->integer()->notNull(),
            'remarks' => $this->text()->null(),
            'quantity' => $this->integer()->null(),
            'received_on' => $this->date()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-inventoy-accesory_type', '{{%inventoy}}', 'accesory_type');
        $this->createIndex('idx-inventoy-inventory_item_id', '{{%inventoy}}', 'inventory_item_id');
        $this->createIndex('idx-inventoy-supplier_id', '{{%inventoy}}', 'supplier_id');

        $this->addForeignKey(
            'fk-inventoy-inventory_item_id',
            '{{%inventoy}}',
            'inventory_item_id',
            '{{%inventory_items}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventoy-supplier_id',
            '{{%inventoy}}',
            'supplier_id',
            '{{%st_suppliers}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->createTable('{{%inventory_dispersals}}', [
            'id' => $this->primaryKey(),
            'accesory_type' => $this->string(50)->notNull(),
            'inventory_item_id' => $this->integer()->notNull(),
            'dispersed_to' => $this->string(20)->notNull(),
            'teacher_id' => $this->integer()->null(),
            'grade_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'academic_year_id' => $this->integer()->notNull(),
            'term_id' => $this->integer()->notNull(),
            'dispersed_on' => $this->date()->notNull(),
            'qty_dispersed' => $this->integer()->notNull()->defaultValue(0),
            'is_to_be_returned' => $this->boolean()->notNull()->defaultValue(false),
            'returned_on' => $this->date()->null(),
            'qty_returned' => $this->integer()->notNull()->defaultValue(0),
            'missplaced' => $this->integer()->notNull()->defaultValue(0),
            'remarks' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-inventory_dispersals-accesory_type', '{{%inventory_dispersals}}', 'accesory_type');
        $this->createIndex('idx-inventory_dispersals-inventory_item_id', '{{%inventory_dispersals}}', 'inventory_item_id');
        $this->createIndex('idx-inventory_dispersals-dispersed_to', '{{%inventory_dispersals}}', 'dispersed_to');
        $this->createIndex('idx-inventory_dispersals-teacher_id', '{{%inventory_dispersals}}', 'teacher_id');
        $this->createIndex('idx-inventory_dispersals-grade_id', '{{%inventory_dispersals}}', 'grade_id');
        $this->createIndex('idx-inventory_dispersals-student_id', '{{%inventory_dispersals}}', 'student_id');
        $this->createIndex('idx-inventory_dispersals-academic_year_id', '{{%inventory_dispersals}}', 'academic_year_id');
        $this->createIndex('idx-inventory_dispersals-term_id', '{{%inventory_dispersals}}', 'term_id');

        $this->addForeignKey(
            'fk-inventory_dispersals-inventory_item_id',
            '{{%inventory_dispersals}}',
            'inventory_item_id',
            '{{%inventory_items}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventory_dispersals-teacher_id',
            '{{%inventory_dispersals}}',
            'teacher_id',
            '{{%teachers}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventory_dispersals-grade_id',
            '{{%inventory_dispersals}}',
            'grade_id',
            '{{%st_grades}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventory_dispersals-student_id',
            '{{%inventory_dispersals}}',
            'student_id',
            '{{%st_parents}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventory_dispersals-academic_year_id',
            '{{%inventory_dispersals}}',
            'academic_year_id',
            '{{%st_academic_years}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-inventory_dispersals-term_id',
            '{{%inventory_dispersals}}',
            'term_id',
            '{{%st_terms}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-inventory_dispersals-term_id', '{{%inventory_dispersals}}');
        $this->dropForeignKey('fk-inventory_dispersals-academic_year_id', '{{%inventory_dispersals}}');
        $this->dropForeignKey('fk-inventory_dispersals-student_id', '{{%inventory_dispersals}}');
        $this->dropForeignKey('fk-inventory_dispersals-grade_id', '{{%inventory_dispersals}}');
        $this->dropForeignKey('fk-inventory_dispersals-teacher_id', '{{%inventory_dispersals}}');
        $this->dropForeignKey('fk-inventory_dispersals-inventory_item_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-term_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-academic_year_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-student_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-grade_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-teacher_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-dispersed_to', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-inventory_item_id', '{{%inventory_dispersals}}');
        $this->dropIndex('idx-inventory_dispersals-accesory_type', '{{%inventory_dispersals}}');
        $this->dropTable('{{%inventory_dispersals}}');

        $this->dropForeignKey('fk-inventoy-supplier_id', '{{%inventoy}}');
        $this->dropForeignKey('fk-inventoy-inventory_item_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-supplier_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-inventory_item_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-accesory_type', '{{%inventoy}}');
        $this->dropTable('{{%inventoy}}');

        $this->dropForeignKey('fk-stock_levels-inventory_item_id', '{{%stock_levels}}');
        $this->dropIndex('uq-stock_levels-inventory_item_id', '{{%stock_levels}}');
        $this->dropIndex('idx-stock_levels-inventory_item_id', '{{%stock_levels}}');
        $this->dropTable('{{%stock_levels}}');

        $this->dropIndex('uq-inventory_items-accesory_type-name', '{{%inventory_items}}');
        $this->dropIndex('idx-inventory_items-name', '{{%inventory_items}}');
        $this->dropIndex('idx-inventory_items-accesory_type', '{{%inventory_items}}');
        $this->dropTable('{{%inventory_items}}');
    }
}