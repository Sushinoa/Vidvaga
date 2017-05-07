<?php

namespace app\controllers;

use yii;
use app\models\News;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
class NewsController extends ActiveController
{
    public $modelClass = 'app\models\News';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        //Предоставляем возможность пользоваться нашим АPI посредством ajax-запросов с других доменов
        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            #special rules for particular action
            'actions' => [
//                'view' => [
//                    #web-servers which you alllow cross-domain access
//                    'Origin' => ['*'],
//                    'Access-Control-Request-Method' => ['POST','GET'],
//                    'Access-Control-Request-Headers' => ['*'],
//                    'Access-Control-Allow-Credentials' => true,
//                    'Access-Control-Max-Age' => 86400,
//                    'Access-Control-Expose-Headers' => ['Authorization'],
//                ]
//            ],
//            #common rules
            'cors' => [
               'Origin' => [],
                'Access-Control-Request-Method' => ['GET'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => ['Authorization'],
            ]
            ]
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
            'only' => ['create','update','delete'],
        ];
        //повидение для авторизации,доступ по ролям
        //действующая роль 403 отдает если к методу где нет прав доступа обращаемся,
        // такую роль надо написать по каждому контроллеру
//        $behaviors['access'] = [
//            'class' => AccessControl::className(),
//           // 'only' => ['login', 'logout', 'signup'],
//            'rules' => [
//                [
//                    'allow' => true,
//                    'actions' => ['view','search_id_tags', 'last_id','last_limit'],
//                    'roles' => ['user'],
//                ],
//                [
//                    'allow' => true,
//                    'actions' => ['create','update','delete'],
//                    'roles' => ['admin'],
//                ],
//            ],
//
//        ];
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
     * Событие представление списка новостей
     *
     * @return string
     */
//    public function actionIndex()
//    {
//       $news = News::findAllPublish();
//
//        return $news;
//    }
    /**
     * Событие представление одной новости
     *
     * @return string
     */
    public function actionView($id)
    {
        $news = $this->findModel($id);
        return $news;
    }

    /* function to find the requested record/model */
    protected function findModel($id)
    {
        if (($news = News::findOne($id)) !== null) {

            return $news;

        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Нет такой записи в базе данных"];
        }
    }

    /**
     * Событие создание новости
     *
     * @return string
     */
    public function actionCreate()
    {
        $user = new News();
        $news_data = Yii::$app->request->post();
        $user->load($news_data, '');//передаем второй пустой параметр - название модели или второй вариант: $model->load(array('User'=>'$post_data'));
        if ($user->save()) {
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
        $news = $this->findModel($id);
        $news->title = $post_data['title'];
        $news->text = $post_data['text'];
        $news->type = $post_data['type'];
        $news->tags = $post_data['tags'];
        $news->icon = $post_data['icon'];
        $news->top = $post_data['top'];
        $news->publish = $post_data['publish'];
        $news->date = time();
        $news->image = $post_data['image'];
        if ($news->update()) {
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
     * Событие поиска даты последней новости
     *
     * @return string
     */
    public function actionLast_id()
    {
        $lastId = News::findLast_id();

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = ['message' => "$lastId"];

    }


    /**
     * Событие поиска 15 последних новостей
     *
     * @return array
     */
    public function actionLast_limit()
    {
        $lastNews = News::findLimit();

        return $lastNews;

    }

    /**
     * Событие поиска самого минимального id
     *
     * @return number
     */
    public function actionOld_id()
    {
        $oldNews = News::findOldId();

        return $oldNews;

    }

    /**
     * Событие удаление новости
     *
     * @return string
     */
    public function actionDelete($id)
    {
        $news = $this->findModel($id);
        if ($news->delete()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось удалить запись в базе данных"];
        }
    }

    /**
     * Событие поиска id записей с опрелеленным тегом
     *
     * @return array
     */
    public function actionSearch_id_tags()
    {//{"key":"sport"}
        $post_data = Yii::$app->request->post();
        $publishVStags = News::find()->where(['tags' => $post_data])->all();
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        foreach ($publishVStags as $publish){
            $response -> data[] = $publish->id;//[6,7]
        }
    }

}