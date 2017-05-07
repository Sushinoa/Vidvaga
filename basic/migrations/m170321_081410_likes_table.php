<?php

use yii\db\Migration;

class m170321_081410_likes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('likes', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'news_id' => $this->integer()->notNull(),
            'is_like'=>$this->boolean()->notNull()
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-likes-user_id',
            'likes',
            'user_id'
        );

        $this->addForeignKey(
            'fk-likes-user_id',
            'likes',
            'user_id',
            'likes',
            'id',
            'CASCADE'
        );
        // creates index for column `news_id`
        $this->createIndex(
            'idx-likes-news_id',
            'likes',
            'news_id'
        );

        $this->addForeignKey(
            'fk-likes-news_id',
            'likes',
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
        // drops foreign key for table `questions`
        $this->dropForeignKey(
            'fk-likes-user_id',
            'likes'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-likes-user_id',
            'likes'
        );

        // drops foreign key for table `questions`
        $this->dropForeignKey(
            'fk-likes-news_id',
            'likes'
        );

        // drops index for column `news_id`
        $this->dropIndex(
            'idx-likes-news_id',
            'likes'
        );
        $this->dropTable('likes');
    }
}
