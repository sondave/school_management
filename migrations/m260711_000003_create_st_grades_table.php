<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_grades}}`.
 */
class m260711_000003_create_st_grades_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_grades}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull(),
            'grade' => $this->string(255)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%st_grades}}');
    }
}
