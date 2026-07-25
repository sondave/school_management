<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%user}}` and `{{%user_profile}}`.
 */
class m260723_000028_create_user_tables extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(55)->notNull(),
            'email' => $this->string(100)->notNull(),
            'password_hash' => $this->string(150)->notNull(),
            'auth_key' => $this->string(199)->notNull(),
            'access_token' => $this->string(199)->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'is_first_login' => $this->tinyInteger()->notNull()->defaultValue(1),
            'activation_pas_expires_at' => $this->dateTime()->null(),
            'login_attempts' => $this->tinyInteger()->notNull()->defaultValue(0),
            'last_login_at' => $this->dateTime()->null(),
            'blocked_at' => $this->dateTime()->null(),
            'activated_at' => $this->dateTime()->null(),
            'remarks' => $this->text()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('uq-user-username', '{{%user}}', 'username', true);
        $this->createIndex('uq-user-email', '{{%user}}', 'email', true);
        $this->createIndex('idx-user-status', '{{%user}}', 'status');

        $this->createTable('{{%user_profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'first_name' => $this->string(50)->notNull(),
            'other_names' => $this->string(150)->notNull(),
            'gender' => $this->string(20)->notNull(),
            'phone' => $this->string(20)->notNull(),
            'dob' => $this->date()->notNull(),
        ]);

        $this->createIndex('uq-user_profile-user_id', '{{%user_profile}}', 'user_id', true);
        $this->createIndex('idx-user_profile-phone', '{{%user_profile}}', 'phone');

        $this->addForeignKey(
            'fk-user_profile-user_id',
            '{{%user_profile}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-user_profile-user_id', '{{%user_profile}}');

        $this->dropIndex('idx-user_profile-phone', '{{%user_profile}}');
        $this->dropIndex('uq-user_profile-user_id', '{{%user_profile}}');
        $this->dropTable('{{%user_profile}}');

        $this->dropIndex('idx-user-status', '{{%user}}');
        $this->dropIndex('uq-user-email', '{{%user}}');
        $this->dropIndex('uq-user-username', '{{%user}}');
        $this->dropTable('{{%user}}');
    }
}
