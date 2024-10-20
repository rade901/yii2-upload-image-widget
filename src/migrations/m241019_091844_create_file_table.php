<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%file}}`.
 */
class m241019_091844_create_file_table extends Migration
{
     /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'path_url' => $this->string(),
            'base_url' => $this->string()->notNull(),
            'mime_type' => $this->string()->notNull(),
        ], $tableOptions);

        /// indexes all the columns

        $this->createIndex(
            '{{%idx-file-id}}',
            '{{%file}}',
            'id'
        );

        $this->createIndex(
            '{{%idx-file-name}}',
            '{{%file}}',
            'name'
        );

        $this->createIndex(
            '{{%idx-file-base_url}}',
            '{{%file}}',
            'base_url'
        );

        $this->createIndex(
            '{{%idx-file-mime_type}}',
            '{{%file}}',
            'mime_type'
        );

        $this->createIndex(
            '{{%idx-file-path_url}}',
            '{{%file}}',
            'path_url'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%file}}');
    }
}
