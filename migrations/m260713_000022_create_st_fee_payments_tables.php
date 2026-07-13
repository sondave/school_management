<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%fee_payments}}` and `{{%payment_allocations}}`.
 */
class m260713_000022_create_st_fee_payments_tables extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%fee_payments}}', [
            'id' => $this->primaryKey(),
            'receipt_no' => $this->string(50)->notNull(),
            'student_id' => $this->integer()->notNull(),
            'payment_date' => $this->date()->notNull(),
            'payment_method' => $this->string(50)->notNull(),
            'remarks' => $this->text()->null(),
            'amount' => $this->decimal(12, 2)->notNull(),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex('uq_fee_payments_receipt_no', '{{%fee_payments}}', ['receipt_no'], true);
        $this->createIndex('idx_fee_payments_student_id', '{{%fee_payments}}', ['student_id']);
        $this->createIndex('idx_fee_payments_payment_date', '{{%fee_payments}}', ['payment_date']);

        $this->addForeignKey('fk_fee_payments_student_id', '{{%fee_payments}}', 'student_id', '{{%st_students}}', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('{{%payment_allocations}}', [
            'id' => $this->primaryKey(),
            'payment_id' => $this->integer()->notNull(),
            'student_fee_charge_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(12, 2)->notNull(),
        ]);

        $this->createIndex('idx_payment_allocations_payment_id', '{{%payment_allocations}}', ['payment_id']);
        $this->createIndex('idx_payment_allocations_charge_id', '{{%payment_allocations}}', ['student_fee_charge_id']);

        $this->addForeignKey('fk_payment_allocations_payment_id', '{{%payment_allocations}}', 'payment_id', '{{%fee_payments}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_payment_allocations_charge_id', '{{%payment_allocations}}', 'student_fee_charge_id', '{{%st_student_fee_charges}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_payment_allocations_charge_id', '{{%payment_allocations}}');
        $this->dropForeignKey('fk_payment_allocations_payment_id', '{{%payment_allocations}}');
        $this->dropIndex('idx_payment_allocations_charge_id', '{{%payment_allocations}}');
        $this->dropIndex('idx_payment_allocations_payment_id', '{{%payment_allocations}}');
        $this->dropTable('{{%payment_allocations}}');

        $this->dropForeignKey('fk_fee_payments_student_id', '{{%fee_payments}}');
        $this->dropIndex('idx_fee_payments_payment_date', '{{%fee_payments}}');
        $this->dropIndex('idx_fee_payments_student_id', '{{%fee_payments}}');
        $this->dropIndex('uq_fee_payments_receipt_no', '{{%fee_payments}}');
        $this->dropTable('{{%fee_payments}}');
    }
}
