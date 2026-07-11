<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_template}}`.
 */
class m260711_000018_create_sms_template_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%sms_template}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->string(255)->null(),
            'template' => $this->text()->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->DATETIME(),
            'created_by' => $this->integer(),
            'updated_at' => $this->DATETIME(),
            'updated_by' => $this->integer(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sms_template}}');
    }
}
