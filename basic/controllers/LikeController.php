<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.03.2017
 * Time: 1:49
 */

namespace app\controllers;

use yii;
use app\models\Likes;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
class LikeController extends ActiveController
{
    public $modelClass = 'app\models\Likes';

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
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        //повидение для аутентификации
        //  HttpBearerAuth — реализация аутентификации по токену (HTTP BearerAuth)
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'realm' => 'Protected area',
            'except' => ['options'],
            // 'only' => ['create','update','delete'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        // unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * Событие просмотра новостей которые добавлены в закладки
     *
     * @return array
     */
    public function actionIndex()
    {
        $likesUser = Likes::findLikes();
        var_dump($likesUser);

    }

    /**
     * Событие представление одной закладки
     *
     * @return string
     */
    public function actionView($id)
    {
        $likes = $this->findModel($id);
        return $likes;
    }

    /* function to find the requested record/model */
    protected function findModel($id)
    {
        if (($likes = Likes::findOne($id)) !== null) {

            return $likes;

        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Нет такой записи в базе данных"];
        }
    }
    /**
     * Событие добавления новости в закладки
     *
     * @return string
     */
    public function actionCreate()
    {
        $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $like = new Likes();
        $data = Yii::$app->request->post();
        $data+=['user_id'=>$user_id];
        $like->load($data, '');//передаем второй пустой параметр - название модели или второй вариант: $model->load(array('User'=>'$post_data'));
        if ($like->save()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить данные в бд"];
        }
    }

    /**
     * Событие редактирования закладки
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $post_data = Yii::$app->request->post();
        $likes = $this->findModel($id);
        $likes->is_like = $post_data['is_like'];
        if ($likes->update()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить новые данные в бд"];
        }

    }

}