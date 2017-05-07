<?php

use yii\db\Migration;

class m161015_115410_users_table extends Migration
{
    public $table_users = 'users';

    public function up()
    {
        $this->createTable($this->table_users, [
            'id' => $this->primaryKey(),
            'phone_number'=>$this->integer()->unique()->notNull(),
            'username' => $this->string(128),
            'password' => $this->string(64)->notNull(),
            'email'=>$this->string(128)->unique(),
            'set_email'=>$this->boolean(),
            'district'=>$this->string(256),
            'description'=>$this->text(),
            'confirm_sms_code' => $this->integer(64),
            'confirm_email_code' => $this->string(256),
            'accessToken' => $this->string(),
            'secret_key'=>$this->string(),
            'client'=>$this->string(256),
            'created_at' => $this->integer(),
            'confirmed_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'set_cabinet'=>$this->boolean(),
            'set_mobile'=>$this->boolean(),
            'group'=>$this->string(64)
        ]);
    }

    public function down()
    {
        $this->dropTable($this->table_users);
        return true;
    }

}
