<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_subjects}}`.
 */
class m260711_000002_create_st_subjects_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_subjects}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_subjects-code', '{{%st_subjects}}', 'code', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_subjects-code', '{{%st_subjects}}');
        $this->dropTable('{{%st_subjects}}');
    }
}
