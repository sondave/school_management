<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_fee_categories}}`.
 */
class m260711_000020_create_st_fee_categories_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_fee_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'is_optional' => $this->tinyInteger()->notNull()->defaultValue(0),
            'status' => $this->tinyInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_st_fee_categories_name', '{{%st_fee_categories}}', ['name'], true);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%st_fee_categories}}');
    }
}
