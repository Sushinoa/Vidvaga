<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
/**
 * This is the model class for table "questions".
 *
 * @property integer $id
 * @property string $title
 * @property integer $user_id
 * @property string $text
 * @property integer $news_id
 * @property integer $date
 * @property string $answer
 * @property boolean $review
 *
 * @property News $news
 * @property Users $user
 */
class Question extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['date'], 'default',  'value'=> time()],
            [['user_id', 'news_id', 'date'], 'integer'],
            [['text', 'answer' ], 'string'],
            [['title'], 'string', 'max' => 255],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'user_id' => 'User ID',
            'text' => 'Text',
            'news_id' => 'News ID',
            'date' => 'Date',
            'answer' => 'Answer',
            'review' => 'Review',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasOne(News::className(), ['id' => 'news_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    public static function findAllQuestions()
    {
        return Question::find()->all();
    }

    public static function findById($id)
    {
        return Question::find()->where(['id' => $id]);
    }

    public static function findByNewsId($newsId)
    {
        return Question::find()->where(['news_id' => $newsId] );
    }

    public static function findByDate($date)
    {
        return Question::find()->where(['date' => $date]);
    }
}
