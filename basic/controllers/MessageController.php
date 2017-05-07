<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 20.03.2017
 * Time: 14:32
 */

namespace app\controllers;

use yii;
use app\models\Messages;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
class MessageController extends ActiveController
{
    public $modelClass = 'app\models\Messages';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //Предоставляем возможность пользоваться нашим АPI посредством ajax-запросов с других доменов
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
        ];
        //Отдача ответов в фомате JSON
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats'=>[
                'application/json'=>Response::FORMAT_JSON,
            ]
        ];

        //повидение для аутентификации
        //  HttpBearerAuth — реализация аутентификации по токену (HTTP BearerAuth)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'realm' => 'Protected area',
            'except' => ['options'],
            //  'only' => ['create','update','delete'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * Событие создание sms,email рассылки
     *
     * @return string
     */
    public function actionCreate()
    {
        $message = new Messages();
        $message_data = Yii::$app->request->post();
        $message->load($message_data, '');//передаем второй пустой параметр
        if ($message->save()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить данные в бд"];
        }
    }


}