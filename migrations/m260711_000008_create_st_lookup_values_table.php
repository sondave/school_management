<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_lookup_values}}`.
 */
class m260711_000008_create_st_lookup_values_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_lookup_values}}', [
            'id' => $this->primaryKey(),
            'category' => $this->string(20)->notNull(),
            'code' => $this->string(30)->notNull(),
            'name' => $this->string(150)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_lookup_values-category', '{{%st_lookup_values}}', 'category');
        $this->createIndex('idx-st_lookup_values-category-code-name', '{{%st_lookup_values}}', ['category', 'code', 'name'], true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_lookup_values-category-code-name', '{{%st_lookup_values}}');
        $this->dropIndex('idx-st_lookup_values-category', '{{%st_lookup_values}}');
        $this->dropTable('{{%st_lookup_values}}');
    }
}
