<?php

use artkost\attachment\Manager;
use yii\base\InvalidConfigException;
use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m141207_024254_create_page_table
 */
class m141207_024254_create_attachment_table extends Migration
{
    public $attachmentTable;

    public function init()
    {
        parent::init();

        /** @var Manager $attachment */
        $attachment = Manager::getInstance();

        if (!$attachment instanceof Manager) {
            throw new InvalidConfigException('Attachment Manager component not defined');
        }

        $this->attachmentTable = $attachment->attachmentFileTable;
    }

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        // Post table
        $this->createTable($this->attachmentTable, [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'uri' => Schema::TYPE_STRING . ' NOT NULL',
            'mime' => Schema::TYPE_STRING . ' NOT NULL',
            'size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type' => Schema::TYPE_STRING . ' NOT NULL',
            'status_id' => 'tinyint(4) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL'
        ], $tableOptions);

        // Indexes
        $this->createIndex('type', $this->attachmentTable, 'type');
        $this->createIndex('created_at', $this->attachmentTable, 'created_at');
        $this->createIndex('updated_at', $this->attachmentTable, 'updated_at');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->attachmentTable);
    }
}
