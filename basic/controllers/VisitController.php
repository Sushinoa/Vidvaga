<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.03.2017
 * Time: 10:13
 */

namespace app\controllers;

use yii;
use app\models\Visits;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
class VisitController extends ActiveController
{
    public $modelClass = 'app\models\Visits';

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
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    /**
     * Событие просмотра новостей которые добавлены в закладки
     *
     * @return array
     */
    public function actionIndex()
    {
        $visitUser = Visits::findVisitUser();
        var_dump($visitUser);
    }

    /**
     * Событие представление одной закладки
     *
     * @return string
     */
    public function actionView($id)
    {
        $bookmarks = $this->findModel($id);
        return $bookmarks;
    }

    /* function to find the requested record/model */
    protected function findModel($id)
    {
        if (($visits = Visits::findOne($id)) !== null) {

            return $visits;

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
    {   $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $visit = new Visits();
        $data = Yii::$app->request->post();
        $data+=['user_id'=>$user_id];
        $visit->load($data, '');//передаем второй пустой параметр - название модели или второй вариант: $model->load(array('User'=>'$post_data'));
        if ($visit->save()) {
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
     * Событие редактирование новости
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $post_data = Yii::$app->request->post();
        $visit = $this->findModel($id);
        $visit->is_visit = $post_data['is_visit'];
        if ($visit->update()) {
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