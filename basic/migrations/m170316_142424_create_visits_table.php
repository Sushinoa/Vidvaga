<?php

use yii\db\Migration;

/**
 * Handles the creation of table `visits`.
 */
class m170316_142424_create_visits_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('visits', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'news_id' => $this->integer()->notNull(),
            'is_visit'=>$this->boolean()->notNull()
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-visits-user_id',
            'visits',
            'user_id'
        );

        $this->addForeignKey(
            'fk-visits-user_id',
            'visits',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
        // creates index for column `news_id`
        $this->createIndex(
            'idx-visits-news_id',
            'visits',
            'news_id'
        );

        $this->addForeignKey(
            'fk-visits-news_id',
            'visits',
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
            'fk-visits-user_id',
            'visits'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-visits-user_id',
            'visits'
        );

        // drops foreign key for table `questions`
        $this->dropForeignKey(
            'fk-visits-news_id',
            'visits'
        );

        // drops index for column `news_id`
        $this->dropIndex(
            'idx-visits-news_id',
            'visits'
        );
        $this->dropTable('visits');
    }
}
