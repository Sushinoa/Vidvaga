<?php
namespace app\controllers;

use app\models\ResetPasswordForm;
use app\models\SendEmailForm;
use yii;
use app\models\Users;
use app\models\SignupForm;
use app\models\LoginForm;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\httpclient\Client;
use yii\filters\AccessControl;
class UserController extends ActiveController
{  //Подключение модели юзер
    public $modelClass = 'app\models\Users';

    /**
     * Правила для пользователей
     *
     * @return string
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //Предоставляем возможность пользоваться нашим АPI посредством ajax-запросов с других доменов
        $behaviors['corsFilter'] = [
            'class' => yii\filters\Cors::className(),
        ];
        //Отдача ответов в фомате JSON
        $behaviors['contentNegotiator'] = [
            'class' => yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];
        //повидение для аутентификации
        //  HttpBearerAuth — реализация аутентификации по токену (HTTP Bearer token)
//        $behaviors['authenticator'] = [
//            'class' => HttpBearerAuth::className(),
//            'only' => ['login'],
//            'rules'=> [
//                ['actions'=>['create'],
//                 'allow'=>true,
//                 'roles'=>['?'],
//                ],
//                ['actions'=>['logout'],
//                    'allow'=>true,
//                    'roles'=>['@'],
//                ]
//            ]
//        ];

        return $behaviors;
    }

    /**
     * Переопределение главного - общего события
     *
     * @return string
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Событие представление профиля пользователя
     *
     * @return string
     */
    public function actionView($id)
    {
        $user = $this->findModel($id);
        return $user;
    }

    /* function to find the requested record/model */
    protected function findModel($id)
    {
        if (($user = Users::findOne($id)) !== null) {

            return $user;

        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Нет данных в базе данных о запрашиваемом пользователе"];
        }
      //  return true;
    }

//    /**
//     * Событие регистрации пользователя без ога
//     *
//     * @return string
//     */
//    public function actionCreate()
//    {
//        $register_data = Yii::$app->request->post();
//        unset($register_data['military_id']);
//        $model = new SignupForm();
//        //генерируем смс код для отправки пользователю и сохраняем в бд
//        $confirm_sms_code = mt_rand(1111, 9999);
//        $register_data += ['confirm_sms_code' => $confirm_sms_code];
//        $model->attributes = $register_data;
//
//        if ($model->validate()) {
//            if ($model->signup()) {
//                $response = Yii::$app->response;
//                $response->format = Response::FORMAT_JSON;
//                $response->data = ['message' => "ok"];
//                //подтверждение регистрации
//                //$phone_number = $user->phone_number;
//                // Yii::$app->turbosms->send($confirm_sms_code, ["$phone_number"]);//[number phone, number phone]
//            } else {
//                $response = Yii::$app->response;
//                $response->format = Response::FORMAT_JSON;
//                $response->data = ['message' => "Не удалось сохранить данные в бд"];
//            }
//        } else {
//            $errors = $model->getErrors();
//            $response = Yii::$app->response;
//            $response->format = Response::FORMAT_JSON;
//            $response->data = ['message' => $errors];
//        }
//
//    }

    /**
     * Событие регистрации пользователя с ога
     *
     * @return string
     */
    public function actionCreate()
    {
        $register_data = Yii::$app->request->post();
        $military_id = $register_data['military_id'];
        /*проверка номера участника ато в ога */
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('post')
            ->setUrl('http://oga/basic/web/api/v1/soldiers')//апи ога
            ->setData(['military_id' => $military_id])
            ->send();
        if ($response->isOk) {
            unset($register_data['military_id']);
            if ($response->data['message'] === "ok") {
                $model = new SignupForm();
                //генерируем смс код для отправки пользователю и сохраняем в бд
                $confirm_sms_code = mt_rand(1111, 9999);
                $register_data += ['confirm_sms_code' => $confirm_sms_code];
                $model->attributes = $register_data;

                if ($model->validate()) {
                    if ($model->signup()) {
                       // var_dump($model->phone_number);
                       // echo $model->getAttributeLabel('id');
                        $user = new Users();
                        $phone_number = $register_data['phone_number'];
                        $user_data = $user->findByUsername($phone_number);
                       // var_dump($user_data);
                        $auth = Yii::$app->authManager;
                        $auth->assign($auth->getRole('user'), $user_data['id']);
                        $response = Yii::$app->response;
                        $response->format = Response::FORMAT_JSON;
                        $response->data = ['message' => "ok"];
                        //подтверждение регистрации
                        //$phone_number = $user->phone_number;
                        // Yii::$app->turbosms->send($confirm_sms_code, ["$phone_number"]);//[number phone, number phone]
                    } else {
                        $response = Yii::$app->response;
                        $response->format = Response::FORMAT_JSON;
                        $response->data = ['message' => "Не удалось сохранить данные в бд"];
                    }
                } else {
                    $errors = $model->getErrors();
                    $response = Yii::$app->response;
                    $response->format = Response::FORMAT_JSON;
                    $response->data = ['message' => $errors];
                }
            } else {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = ['message' => "Проверте номер участника АТО"];
            }
        }

    }
    /**
     * Событие подтверждение регистрации через смс
     *
     * @return string
     */
    public function actionVerify()
    {
        $model = new Users();
        $confirm_sms_code = Yii::$app->request->post();
        $user = $model->findUserBySmsCode($confirm_sms_code);
        if ($user) {
            $id = $user['id'];
            $users = Users::findOne($id);
            $users->confirmed_at = time();
            $users->save();
            $group = $user['group'];
            $message = array('group'=>$group);
            $message+=['id'=> $id];
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => $message];
        } else {

            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Sms код введен неверно"];
        }

        //return true;
    }
    /**
     * Событие авторизации и выдачи токена
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $login_data = Yii::$app->request->post();
        $token = mt_rand(11, 99);
        $length = $token;
        $accessToken = Yii::$app->getSecurity()->generateRandomString($length);

        if ($model->load($login_data, '') && $model->login()) {
            $headers = Yii::$app->response->headers;
            $headers->set('Access-Control-Expose-Headers', 'WWW-Authenticate');
            $headers->add('WWW-Authenticate', $accessToken);
            $phone_number = $model['phone_number'];
            $user = Users::findByUsername($phone_number);
            $id = $user['id'];
            $group = $user['group'];
            $user = Users::findOne($id);
            $user->accessToken = $accessToken;
            $user->save();
            $message = array('group'=>$group);
            $message+=['id'=> $id];
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => $message];
        } else {

            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Введены неверные данные"];
        }
       // return true;
    }

    /**
     * Событие редактирования профиля пользователя
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $update_data = Yii::$app->request->post();
        $phone_number = $update_data['phone_number'];
        $email = $update_data['email'];
        $group = $update_data['group'];
        $user = $this->findModel($id);
        $user->phone_number = $phone_number;
        $user->email = $email;
        $user->group = $group;

        if ($user->update()) {
//здесь не хватает логики обновления роли по ид группу поменять = если группа =2 то ремув роле
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];

        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить новые данные в бд"];
        }


    }

    /**
     * Событие удаления профиля пользователя
     *
     * @return string
     */
    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        if ($user->delete()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось удалить запись в базе данных"];
        }

    }
//отправка если забыл пароль письмо в папке мейл
//
    public function actionSend()
    {
        $model = new SendEmailForm();
        $data = Yii::$app->request->post();
        var_dump($data);
        if($model->load($data,'')){
            if( $model->validate()){
                if($model->sendEmail()){
                    $response = Yii::$app->response;
                    $response->format = Response::FORMAT_JSON;
                    $response->data = ['message' => "Проверте Ваш емal"];
                }else{
                    $response = Yii::$app->response;
                    $response->format = Response::FORMAT_JSON;
                    $response->data = ['message' => "Не удалось сбросить пароль"];
                }
            }else {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = ['message' => "Данные не прошли валидацию"];
            }

        }else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось выгрузить данные"];
        }

    }
//если я перешел по ссылке то попадаю на форму ввода нового пароля
    public function actionReset(){

        $model = new ResetPasswordForm();
        $data = Yii::$app->request->post();
        if($model->load($data)){
            if( $model->validate()){
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = ['message' => "ok"];
            }else {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = ['message' => "Данные не прошли валидацию"];
            }
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];

        }else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить данные"];
        }

    }
}