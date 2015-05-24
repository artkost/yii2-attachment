<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m141206_215634_create_page_table
 */
class m141206_215634_create_page_post_table extends Migration
{
    public $postTable = '{{%page_post}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // MySql table options
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';

        // Post table
        $this->createTable($this->postTable, [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'attachment_id' => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'title' => Schema::TYPE_STRING . '(100) NOT NULL',
        ], $tableOptions);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->postTable);
    }
}
