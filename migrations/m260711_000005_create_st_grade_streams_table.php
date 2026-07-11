<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%st_grade_streams}}`.
 */
class m260711_000005_create_st_grade_streams_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%st_grade_streams}}', [
            'id' => $this->primaryKey(),
            'grade_id' => $this->integer()->notNull(),
            'stream_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->dateTime()->null(),
            'created_by' => $this->integer()->null(),
            'updated_at' => $this->dateTime()->null(),
            'updated_by' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-st_grade_streams-grade_id', '{{%st_grade_streams}}', 'grade_id');
        $this->createIndex('idx-st_grade_streams-stream_id', '{{%st_grade_streams}}', 'stream_id');
        $this->createIndex('idx-st_grade_streams-grade_stream_unique', '{{%st_grade_streams}}', ['grade_id', 'stream_id'], true);

        $this->addForeignKey('fk-st_grade_streams-grade_id', '{{%st_grade_streams}}', 'grade_id', '{{%st_grades}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-st_grade_streams-stream_id', '{{%st_grade_streams}}', 'stream_id', '{{%st_streams}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk-st_grade_streams-stream_id', '{{%st_grade_streams}}');
        $this->dropForeignKey('fk-st_grade_streams-grade_id', '{{%st_grade_streams}}');
        $this->dropIndex('idx-st_grade_streams-grade_stream_unique', '{{%st_grade_streams}}');
        $this->dropIndex('idx-st_grade_streams-stream_id', '{{%st_grade_streams}}');
        $this->dropIndex('idx-st_grade_streams-grade_id', '{{%st_grade_streams}}');
        $this->dropTable('{{%st_grade_streams}}');
    }
}
