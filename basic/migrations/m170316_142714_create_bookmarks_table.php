<?php

use yii\db\Migration;

/**
 * Handles the creation of table `bookmarks`.
 */
class m170316_142714_create_bookmarks_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('bookmarks', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'news_id' => $this->integer()->notNull(),
            'is_bookmark'=>$this->integer()->notNull(),
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-bookmarks-user_id',
            'bookmarks',
            'user_id'
        );

        $this->addForeignKey(
            'fk-bookmarks-user_id',
            'bookmarks',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
        // creates index for column `news_id`
        $this->createIndex(
            'idx-bookmarks-news_id',
            'bookmarks',
            'news_id'
        );

        $this->addForeignKey(
            'fk-bookmarks-news_id',
            'bookmarks',
            'news_id',
            'news',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('bookmarks');
    }
}
