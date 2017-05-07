<?php

use yii\db\Migration;

class m170224_111755_questions_table extends Migration
{
    public $table_questions = 'questions';
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('questions', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'news_id' => $this->integer()->notNull(),
            'date'=>$this->integer()->notNull()
        ]);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-questions-user_id',
            'questions',
            'user_id'
        );

        $this->addForeignKey(
            'fk-questions-user_id',
            'questions',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
        // creates index for column `news_id`
        $this->createIndex(
            'idx-questions-news_id',
            'questions',
            'news_id'
        );

        $this->addForeignKey(
            'fk-questions-news_id',
            'questions',
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
            'fk-questions-user_id',
            'questions'
        );

        // drops index for column `users_id`
        $this->dropIndex(
            'idx-questions-user_id',
            'questions'
        );

        // drops foreign key for table `questions`
        $this->dropForeignKey(
            'fk-questions-news_id',
            'questions'
        );

        // drops index for column `news_id`
        $this->dropIndex(
            'idx-questions-news_id',
            'questions'
        );


        $this->dropTable('questions');
    }
}
