<?php

use yii\db\Migration;

class m170328_080517_quest_table extends Migration
{
    public function up()
    {
        $this->createTable($this->table_questionsall,[
            'id' => $this->primaryKey(),
            'title'=>$this->string(256)->notNull(),
            'text'=>$this->text()->notNull(),
            'date'=>$this->integer(),

        ]);
    }

    public function down()
    {
        $this->dropTable('questionsall');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
