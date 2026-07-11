<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_school_info}}`.
 */
class m260711_000001_create_st_school_info_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_school_info}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'website' => $this->string(255)->null(),
            'email' => $this->string(255)->null(),
            'phone_number' => $this->string(30)->notNull(),
            'county' => $this->string(100)->notNull(),
            'physical_address' => $this->string(255)->notNull(),
            'postal_address' => $this->string(255)->null(),
            'school_type' => $this->string(100)->notNull(),
            'motto' => $this->string(255)->null(),
            'mission' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%st_school_info}}');
    }
}
