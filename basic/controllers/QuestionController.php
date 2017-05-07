<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.02.2017
 * Time: 14:43
 */

namespace app\controllers;

use yii;
use app\models\Question;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\ContentNegotiator;
class QuestionController extends ActiveController
{
    public $modelClass = 'app\models\Question';

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
            // 'only' => ['create','update','delete'],
        ];

        return $behaviors;
    }

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
     * Событие представление одной новости
     *
     * @return string
     */
    public function actionView($id)
    {
        $question = $this->findModel($id);
        return $question;
    }

    /* function to find the requested record/model */
    protected function findModel($id)
    {
        if (($question = Question::findOne($id)) !== null) {

            return $question;

        } else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Нет такой записи в базе данных"];
        }
    }

//    /**
//     * Событие задать вопрос по новости
//     *
//     * @return string
//     */
//    public function actionCreate()
//    {
//        $question_data = Yii::$app->request->post();
//        var_dump( $question_data);
//        //var_dump($question_data);
////        $news_id = $question_data['news_id'];
////        $user_id = $question_data['user_id'];
////        $user = Users::findOne($user_id);
////        $news = News::findOne($news_id);
//        $question = new Question();
////        $question->text=$question_data['text'];
////        $question->title=$question_data['title'];
////        $question->date=$question_data['date'];
////       $question->link('user', $user);
////        $question->link('news', $news);работает когда один объект надо связать
//        $question->load($question_data,'');
//        if ($question->save()) {
//            $response = Yii::$app->response;
//            $response->format = Response::FORMAT_JSON;
//            $response->data = ['message' => "ok"];
//        }else {
//            $response = Yii::$app->response;
//            $response->format = Response::FORMAT_JSON;
//            $response->data = ['message' => "Не удалось сохранить данные в бд"];
//        }
//    }
    /**
     * Событие затать вопрос по новости
     *
     * @return string
     */
    public function actionCreate()
    {
        $question_data = Yii::$app->request->post();
        $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $question_data+=['user_id'=>$user_id];
        $question = new Question();
        $question->load($question_data,'');
        if ($question->save()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        }else {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить данные в бд"];
        }
    }

    /**
     * Событие обновления полей в БД "questions"
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $post_data = Yii::$app->request->post();
        $answer = $post_data['answer'];
        $review = $post_data['review'];
        $question = $this->findModel($id);
        $question->answer = $answer;
        $question->review = $review;
        $question->date = time();
        if ($question->update()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "ok"];
        }
        else
        {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = ['message' => "Не удалось сохранить новые данные в бд"];
        }

    }

}