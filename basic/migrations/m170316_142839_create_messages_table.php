<?php

use yii\db\Migration;

/**
 * Handles the creation of table `messages`.
 */
class m170316_142839_create_messages_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('messages', [
            'id' => $this->primaryKey(),
            'sms_text'=>$this->string(),
            'email_text'=>$this->string(),
            'filter_list'=>$this->string(),
            'date'=>$this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('messages');
    }
}
