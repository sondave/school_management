<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_notifications}}`.
 */
class m260711_000019_create_sms_notifications_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%sms_notifications}}', [
            'id' => $this->primaryKey(),
            'tracking_id' => $this->string(64)->notNull(),
            'sms_template_id' => $this->integer()->null(),
            'parent_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'grade_id' => $this->integer()->null(),
            'recipient_type' => $this->string(30)->notNull(),
            'phone_number' => $this->string(20)->notNull(),
            'message' => $this->text()->notNull(),
            'status' => $this->tinyInteger()->notNull()->defaultValue(0),
            'short_code' => $this->string(100)->null(),
            'message_id' => $this->string(100)->null(),
            'network_id' => $this->string(100)->null(),
            'response_code' => $this->string(100)->null(),
            'response_description' => $this->text()->null(),
            'sent_at' => $this->dateTime()->null(),
            'delivered_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx_sms_notifications_tracking_id', '{{%sms_notifications}}', ['tracking_id']);
        $this->createIndex('idx_sms_notifications_status', '{{%sms_notifications}}', ['status']);
        $this->createIndex('idx_sms_notifications_parent_id', '{{%sms_notifications}}', ['parent_id']);
        $this->createIndex('idx_sms_notifications_grade_id', '{{%sms_notifications}}', ['grade_id']);
        $this->createIndex('idx_sms_notifications_template_id', '{{%sms_notifications}}', ['sms_template_id']);

        $this->addForeignKey(
            'fk_sms_notifications_template_id',
            '{{%sms_notifications}}',
            'sms_template_id',
            '{{%sms_template}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sms_notifications_parent_id',
            '{{%sms_notifications}}',
            'parent_id',
            '{{%st_parents}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_sms_notifications_grade_id',
            '{{%sms_notifications}}',
            'grade_id',
            '{{%st_grades}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_sms_notifications_grade_id', '{{%sms_notifications}}');
        $this->dropForeignKey('fk_sms_notifications_parent_id', '{{%sms_notifications}}');
        $this->dropForeignKey('fk_sms_notifications_template_id', '{{%sms_notifications}}');

        $this->dropTable('{{%sms_notifications}}');
    }
}
