<?php

namespace app\models;

use Yii;
use \yii\db\ActiveRecord;
/**
 * This is the model class for table "news".
 *
 * @property integer  $id
 * @property string  $title
 * @property string  $text
 * @property integer $date
 * @property string  $image
 * @property string  $icon
 * @property boolean $top
 * @property integer $publish
 * @property string  $video
 * @property string  $type
 * @property string  $tags
 * @property integer $count_visit
 */
class News extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * Поиск даты последней новости
     *
     * @return string
     */
    public static function findLast_date()
    {
        $lastDate = News::find()->max('date');
        return $lastDate;

    }
    /**
     * Поиск новости у которой минимальное id
     *
     * @return number
     */
    public static function findOldId()
    {
        $OldId = News::find()->min('id');
        return $OldId;

    }
    /**
     * Поиск списка опубликованных новостей
     *
     * @return array
     */
    public static function findAllPublish()
    {
       $newsPublish = News::find()->where(['publish' => 1])->all();
        return $newsPublish;

    }
    /**
     * Поиск последних 15 опубликованных новостей по последней дате
     *
     * @return array
     */
    public static function findLimit()
    {

        $lastNews = News::find()->limit(15)->where(['publish' => 1])->orderBy(['date' => SORT_DESC])->all();
        return  $lastNews;

    }

    /**
     * Поиск идентификатора последней новости
     *
     * @return string
     */
    public static function findLast_id()
    {
        $lastDate = News::findLast_date();
        $lastNews = News::find()->where(['date' => "$lastDate"])->one();
        return $lastNews->id;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['title', 'text'], 'required'],
            [['text'], 'string'],
            [['date','count_visit','publish','top'], 'integer'],
            [['date'], 'default',  'value'=> time()],
            [['title', 'tags'], 'string', 'max' => 256],
            [['image', 'icon', 'video'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'text' => Yii::t('app', 'Text'),
            'date' => Yii::t('app', 'Date'),
            'image' => Yii::t('app', 'Image'),
            'icon' => Yii::t('app', 'Icon'),
            'video' => Yii::t('app', 'Video'),
            'type' => Yii::t('app', 'Type'),
            'tags' => Yii::t('app', 'Tags'),
            'top' => Yii::t('app', 'Top'),
            'publish' => Yii::t('app', 'Publish'),
            'count_visit' => Yii::t('app', 'Count Visit'),
        ];
    }
}
