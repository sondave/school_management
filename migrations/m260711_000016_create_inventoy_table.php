<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%inventory_items}}` and `{{%inventoy}}`.
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
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-inventoy-supplier_id', '{{%inventoy}}');
        $this->dropForeignKey('fk-inventoy-inventory_item_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-supplier_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-inventory_item_id', '{{%inventoy}}');
        $this->dropIndex('idx-inventoy-accesory_type', '{{%inventoy}}');
        $this->dropTable('{{%inventoy}}');

        $this->dropIndex('uq-inventory_items-accesory_type-name', '{{%inventory_items}}');
        $this->dropIndex('idx-inventory_items-name', '{{%inventory_items}}');
        $this->dropIndex('idx-inventory_items-accesory_type', '{{%inventory_items}}');
        $this->dropTable('{{%inventory_items}}');
    }
}