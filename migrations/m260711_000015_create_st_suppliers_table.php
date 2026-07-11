<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_suppliers}}`.
 */
class m260711_000015_create_st_suppliers_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_suppliers}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'source_type' => $this->string(50)->notNull(),
            'phone' => $this->string(30)->null(),
            'email' => $this->string(255)->null(),
            'address' => $this->string(255)->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_suppliers-name', '{{%st_suppliers}}', 'name');
        $this->createIndex('idx-st_suppliers-source_type', '{{%st_suppliers}}', 'source_type');
        $this->createIndex('idx-st_suppliers-email', '{{%st_suppliers}}', 'email');
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_suppliers-email', '{{%st_suppliers}}');
        $this->dropIndex('idx-st_suppliers-source_type', '{{%st_suppliers}}');
        $this->dropIndex('idx-st_suppliers-name', '{{%st_suppliers}}');
        $this->dropTable('{{%st_suppliers}}');
    }
}