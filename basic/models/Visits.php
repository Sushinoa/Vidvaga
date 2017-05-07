<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "visits".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $news_id
 * @property integer $is_visit
 *
 * @property News $news
 * @property Users $user
 */
class Visits extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visits';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'news_id'], 'required'],
            [['user_id', 'news_id', 'is_visit'], 'integer'],
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
            'user_id' => 'User ID',
            'news_id' => 'News ID',
            'is_visit' => 'Is Visit',
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

    /**
     * Поиск списка событий которые пользователь хотел бы посетить
     *
     * @return array
     */
    public static function findVisitUser()
    {   $user=Yii::$app->user->identity;
        $user_id = $user['id'];
        $visit = Visits::find()->where(['user_id' => $user_id])->all();
        return $visit;

    }

}
