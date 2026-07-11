<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_terms}}`.
 */
class m260711_000011_create_st_terms_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_terms}}', [
            'id' => $this->primaryKey(),
            'academic_year_id' => $this->integer()->notNull(),
            'name' => $this->string(100)->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'current' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_terms-academic_year_id', '{{%st_terms}}', 'academic_year_id');
        $this->addForeignKey(
            'fk-st_terms-academic_year_id',
            '{{%st_terms}}',
            'academic_year_id',
            '{{%st_academic_years}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-st_terms-academic_year_id', '{{%st_terms}}');
        $this->dropIndex('idx-st_terms-academic_year_id', '{{%st_terms}}');
        $this->dropTable('{{%st_terms}}');
    }
}
