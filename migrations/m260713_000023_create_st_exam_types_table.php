<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_exam_types}}`.
 */
class m260713_000023_create_st_exam_types_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_exam_types}}', [
            'id' => $this->primaryKey(),
            'code' => $this->string(50)->notNull(),
            'name' => $this->string(50)->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_exam_types-code', '{{%st_exam_types}}', 'code', true);
        $this->createIndex('idx-st_exam_types-name', '{{%st_exam_types}}', 'name', true);
    }

    public function safeDown(): void
    {
        $this->dropIndex('idx-st_exam_types-name', '{{%st_exam_types}}');
        $this->dropIndex('idx-st_exam_types-code', '{{%st_exam_types}}');
        $this->dropTable('{{%st_exam_types}}');
    }
}
