<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 22.03.2017
 * Time: 9:51
 */

namespace app\models;

use yii;
use yii\base\Model;
class SendEmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
             ['email','required'],
             ['email','email'],
             ['email', 'exist',
                'targetClass'=>Users::className(),
//                 'filter'=>[
//                     'status'=>Users::STATUS_ACTIVE
//                 ],
                 'message'=>'Данный еmail не зарегестрирован'
             ],
        ];
    }

//    public function attributeLabels()
//    {
//        return [
//            'email'=>'Email'
//        ];
//    }

    public function sendEmail()
    {
        /*@var $user Users*/
        $user = Users::findOne(
            [
//                'status'=>Users::STATUS_ACTIVE,
                'email'=>$this->email
            ]
        );

        if($user) {
            $user->generateSecretKey();
                if($user->save()){
                   // var_dump(Yii::$app->mailer->compose('resetPassword',['user'=>$user]));
                    return Yii::$app->mailer->compose('resetpassword',['user'=>$user])
                        ->setFrom([Yii::$app->params['supportEmail']=>Yii::$app->name.'(отправлено роботом)'])
                        ->setTo($this->email)
                        ->setSubject('Сброс пароля для '.Yii::$app->name)
                        ->send();
                }
        }

        return false;
    }

}