<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 22.03.2017
 * Time: 12:14
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;
class ResetPasswordForm extends Model
{
   public $password;
   private $_user;

   public function rules()
   {
       return [
           ['password','required']
       ];
   }

    public function __construct($key, $config=[])
    {
        if(empty($key)||lis_string($key)){
            throw new InvalidParamException('Ключ не может быть пустым');
            $this->_user = Users::findBySecretKey($key);
            if($this->_users){
                throw new InvalidParamException('Не верный ключ');
                parent::_construct($config);
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'password'=>'Пароль'
        ];
    }
    //метод для сброса пароля
    public function resetPassword()
    {
        /*@var $user Users*/
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removeSecretKey();
        return $user->save();

    }
}