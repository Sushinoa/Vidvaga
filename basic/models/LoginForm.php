<?php

namespace app\models;

use Yii;
use yii\base\Model;
/**
 * LoginForm is the model behind the login form.
 *
 * @property Users|null $user This property is read-only.
 * @property string $phone_number
 * @property string $password
 *
 */

class LoginForm extends Model
{

    public $phone_number;

    public $password;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['phone_number', 'password'], 'required'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'phone_number' => Yii::t('app','Number phone'),
            'password' => Yii::t('app','password'),

        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param array $params the additional name-value pairs given in the rule
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {

                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            return Yii::$app->user->login($this->getUser());//, $this->rememberMe ? 3600*24*30 : 0
        }
        return false;
    }
//    /**
//     * * Logs in a user by the given access token.
//     * @return bool whether the user is logged in successfully
//     */
//    public function loginByAccessToken()
//    {
//        if ($this->validate()) {
//
//            return Yii::$app->user->loginByAccessToken($this->getUser());
//
//        }
//        return false;
//    }

    /**
     * * Generates a random string of specified length..
     * @return string the generated random key
     */

//    public function generateUniqueRandomString($attribute, $length = 32) {
//
//        $randomString = Yii::$app->getSecurity()->generateRandomString($length);
//        if(!$this->findOne([$attribute => $randomString]))
//            return $randomString;
//        else
//            return $this->generateUniqueRandomString($attribute, $length);
//
//    }

    /**
     * Finds user by [[username]]
     *
     * @return Users|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Users::findByUsername($this->phone_number);

        }

        return $this->_user;
    }

}
