<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "likes".
 *
 * @property integer $id
 * @property integer $news_id
 * @property integer $user_id
 * @property boolean $is_like
 */
class Likes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'likes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['news_id', 'user_id'], 'required','message' => 'News_id, user_id must be required'],
            [['news_id', 'user_id'], 'integer','message' => 'News_id, user_id must be integer'],
            [['news_id'], 'exist', 'skipOnError' => true, 'targetClass' => News::className(), 'targetAttribute' => ['news_id' => 'id'],'message' => 'Must be links table News'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id'],'message' => 'Must be links table Users'],
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
            'is_like' => 'Is Like',
        ];
    }

    /**
     * Поиск likes
     *
     * @return array
     */
    public static function findLikes()
    {
        $likes = Likes::find()->all();
        return $likes;

    }
}
