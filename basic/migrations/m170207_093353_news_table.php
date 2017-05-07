<?php

use yii\db\Migration;

class m170207_093353_news_table extends Migration
{
    public $table_news = 'news';

    public function up()
    {

        $this->createTable($this->table_news, [
            'id' => $this->primaryKey(),
            'title'=>$this->string(256)->notNull(),
            'text'=>$this->text()->notNull(),
            'date'=>$this->integer(),
            'image'=>$this->string(),
            'icon'=>$this->string(),
            'video'=>$this->string(),
            'type' => $this->string(64),
            'likes'=>$this->boolean(),
            'tags'=>$this->string(256),
            'visit'=>$this->boolean(),
            'top'=>$this->boolean(),
            'publish'=>$this->integer(),

        ]);
    }

    public function down()
    {
        $this->dropTable($this->table_news);
        return true;
    }

}