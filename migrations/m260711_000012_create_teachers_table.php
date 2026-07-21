<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teachers}}`.
 */
class m260711_000012_create_teachers_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%teachers}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(100)->notNull(),
            'other_names' => $this->string(150)->notNull(),
            'phone_number' => $this->string(20)->notNull(),
            'alternate_phone_number' => $this->string(20)->null(),
            'email_address' => $this->string(100)->notNull(),
            'date_of_birth' => $this->date()->notNull(),
            'employment_type' => $this->string(20)->notNull(),
            'tsc_number' => $this->string(50)->null(),
            'first_appointment_date' => $this->date()->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-teachers-phone_number-unique', '{{%teachers}}', 'phone_number', true);
        $this->createIndex('idx-teachers-alternate_phone_number-unique', '{{%teachers}}', 'alternate_phone_number', true);
        $this->createIndex('idx-teachers-email_address-unique', '{{%teachers}}', 'email_address', true);
        $this->createIndex('idx-teachers-tsc_number-unique', '{{%teachers}}', 'tsc_number', true);
        
    }

    public function safeDown(): void
    {

        $this->dropIndex('idx-teachers-tsc_number-unique', '{{%teachers}}');
        $this->dropIndex('idx-teachers-email_address-unique', '{{%teachers}}');
        $this->dropIndex('idx-teachers-alternate_phone_number-unique', '{{%teachers}}');
        $this->dropIndex('idx-teachers-phone_number-unique', '{{%teachers}}');
        $this->dropTable('{{%teachers}}');
    }
}
