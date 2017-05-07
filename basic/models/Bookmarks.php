<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "bookmarks".
 *
 * @property integer $id
 * @property integer $news_id
 * @property integer $user_id
 * @property boolean $is_bookmark
 *
 * @property News $news
 * @property Users $user
 */
class Bookmarks extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bookmarks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id', 'user_id'], 'required'],
            [['news_id', 'user_id'], 'integer'],
            [['is_bookmark',], 'boolean'],
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
            'news_id' => 'News ID',
            'user_id' => 'User ID',
            'is_bookmark' => 'Is Bookmarks',
        ];
    }

    /**
     * Поиск списка новостей которые добавленны в закладки
     *
     * @return array
     */
    public static function findBookmarksUser()
    {   $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $bookmarks = Bookmarks::find()->where(['user_id' => $user_id])->all();
        return $bookmarks;

    }

    /**
     * Поиск списка новостей которые добавленны в закладки
     *
     * @return array
     */
    public static function findBookmarks()
    {   $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $bookmarks = Bookmarks::find()->where(['user_id' => $user_id])->one();
        return $bookmarks;

    }
}
