<?php

namespace app\models;

use yii;
use \yii\db\ActiveRecord;
use \yii\web\IdentityInterface;
//при совпадении данных регистрации ловим с уже имеющейся базой данных
//отлавливаем ошибку исключением - Exception
use yii\db\IntegrityException;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $phone_number
 * @property string $password
 * @property string $email
 * @property boolean $set_email
 * @property string $district
 * @property string $description
 * @property integer $confirm_sms_code
 * @property string $confirm_email_code
 * @property string $accessToken
 * @property string $secret_key
 * @property string $client
 * @property integer $created_at
 * @property integer $confirmed_at
 * @property integer $updated_at
 * @property boolean $set_mobile
 * @property boolean $set_cabinet
 * @property string $group
 */

class Users extends ActiveRecord implements IdentityInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'default', 'value'=>time()],
            [['phone_number', 'password'], 'required'],
            [['created_at', 'confirmed_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['email','phone_number'], 'string', 'max' => 128],
            [['email'], 'email'],
            [['group'], 'string', 'max' => 64],
            [['group'], 'default', 'value' => '0'],
            [['password'], 'string', 'min' => 6, 'max' => 64],
            [[ 'district', 'confirm_email_code', 'accessToken'], 'string'],
            [['accessToken','secret_key'], 'unique'],
            [['confirm_sms_code'], 'integer'],
            [['email', 'phone_number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'set_email' => Yii::t('app', 'Set Avatar'),
            'district' => Yii::t('app', 'District'),
            'description' => Yii::t('app', 'Description'),
            'confirm_sms_code' => Yii::t('app', 'Confirm Sms Code'),
            'confirm_email_code' => Yii::t('app', 'Confirm Email Code'),
            'accessToken' => Yii::t('app', 'Access Token'),
            'created_at' => Yii::t('app', 'Created At'),
            'confirmed_at' => Yii::t('app', 'Confirmed At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'set_mobile' => Yii::t('app', 'Set Cabinet'),
            'set_cabinet' => Yii::t('app', 'Set Cabinet'),
            'group' => Yii::t('app', 'Group'),
        ];
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);//логика сравнения - валидации
    }


    public function beforeValidate(){
        if($this->isNewRecord){
            $this->password = Yii::$app->security->generatePasswordHash($this->password);//получаем на выходе хеш сумму и пароль
//            $this->confirm_email_code = md5(microtime().self::className());
        }

        return parent::beforeValidate();
    }


    /**
     * @return string
     */
    public function getUsername(){
        return $this->phone_number;
    }


    /**
     * отлов повторного емейла, номера телефона и карты бойца в базе
     * $runValidation boolean
     * $attributeNames boolean
     *
     * $e var error
     * return $result string
     */
/*    public function save($runValidation=true, $attributeNames=null)
    {
        $result = false;
        try{
            $result = parent::save($runValidation, $attributeNames);
        } catch(IntegrityException $e)
        {
            $this->addError('email', 'This email is not correct! duplicate email');
//            $this->addError('military_id', 'This email is not correct! duplicate email');
//            $this->addError('phone_number', 'This email is not correct! duplicate email');
//            $this->addError('username', 'This email is not correct! duplicate email');
        }
        return $result;
    }*/

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }



    /**Поиск и проверка токена
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by phone_number
     *
     * @param string $phone_number
     * @return array
     */
    public static function findByUsername($phone_number)
    {
        return self::find()->where(['phone_number' => $phone_number])->orWhere(['phone_number' => $phone_number])->one();
    }

    /**
     * Finds user by confirm_sms_code
     *
     * @param string $confirm_sms_code
     * @return array
     */
    public static function findUserBySmsCode($confirm_sms_code)
    {
        return self::find()->where(['confirm_sms_code' => $confirm_sms_code])->one();

    }
    /**
     *Создаем из случайной строки и текущего времени одну строку и присваеваем полученное значение свойству
     * $secret_key объекта пользователя.Перед сбровом пароля или активацией аккаунта,ключ будет записан в бд по емайл
     */
    public function generateSecretKey()
    {
        $this->secret_key = Yii::$app->security->generateRandomString().'_'.time();
    }
    /**
     *  Присваиваем null secret_key объекту пользователя и записываем в бд.После сброса пароля или активации аккаунта
     */
    public function removeSecretKey()
    {
        $this->secret_key = null;
    }
    /**
     *Проверка метода isSecretKeyExpire если true достаем из бд объект пользовтаеля с переданным ключом  $key
     */
    public static function findBySecretKey($key)
    {
        if(!static::isSecretKeyExpire($key))
            return null;
        return static::findOne(
            [
                'secret_key'=>$key
            ]
        );
    }
    /**
     *Хэлперы
     */
    public static function isSecretKeyExpire($key)
    {
        if(empty($key))
            return false;
        $expire = Yii::$app->params['secretKeyExpire'];//переменная $expire равна сроку действия секретного ключа
        //разбиваем строку на массив (разделитель знак _),где первый элемнет будет
        //ранее сгенерированный ключ,второй элемент - время создания ключа
        $parts = explode('_',$key);
        $timestamp = (int)end($parts);//Помещаем в переменную последний элемент массива, т.е. время создания ключа
        return $timestamp + $expire >= time();//складывает время созданя ключа и время действия ключа если больше,
        //либо равно возвращаем true а иначе false

    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }
}
