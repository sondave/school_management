<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_academic_years}}`.
 */
class m260711_000010_create_st_academic_years_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_academic_years}}', [
            'id' => $this->primaryKey(),
            'year' => $this->string(20)->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'current' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_academic_years-year', '{{%st_academic_years}}', 'year', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_academic_years-year', '{{%st_academic_years}}');
        $this->dropTable('{{%st_academic_years}}');
    }
}
