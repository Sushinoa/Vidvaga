<?php

namespace app\models;

use yii;
use yii\base\Model;
/**
 * RegisterForm is the model behind the login form.
 *
 * @property Users|null $user This property is read-only.
 * @property string $phone_number
 * @property integer $confirm_sms_code
 * @property string $email
 * @property string $password
 */
class SignupForm extends Model
{
    public $email;

    public $phone_number;

    public $password;

    public $confirm_sms_code;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password', 'phone_number'], 'required'],
            ['phone_number', 'unique','targetClass'=>'app\models\Users','message' => 'This phone_number has already been taken.'],
            [['email'], 'email'],
            ['email', 'unique','targetClass'=>'app\models\Users','message' => 'This email has already been taken.'],
            [['phone_number'], 'string', 'max' => 64],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [['confirm_sms_code'], 'integer'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return Users|null the saved model or null if saving fails
     */
    public function signup()
    {
        $user = new Users();
        $user->load($this->attributes,'');
        $user->phone_number=$this->phone_number;
        $user->password=$this->password;
        $user->email=$this->email;
        $user->confirm_sms_code=$this->confirm_sms_code;
        $user->save();

        return $user;

    }

    public function findUser(){

        //$model->phone_number;

    }

}
