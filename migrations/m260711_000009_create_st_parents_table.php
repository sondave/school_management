<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_parents}}`.
 */
class m260711_000009_create_st_parents_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_parents}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'other_names' => $this->string(150)->notNull(),
            'gender' => $this->string(20)->notNull(),
            'national_id' => $this->string(20)->null(),
            'date_of_birth' => $this->date()->null(),
            'phone_no' => $this->string(20)->notNull(),
            'alternate_phone_no' => $this->string(20)->null(),
            'email' => $this->string(255)->null(),
            'county' => $this->string(100)->notNull(),
            'physical_address' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_parents-phone_no', '{{%st_parents}}', 'phone_no');
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_parents-phone_no', '{{%st_parents}}');
        $this->dropTable('{{%st_parents}}');
    }
}
