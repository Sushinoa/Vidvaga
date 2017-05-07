<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "question".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property integer $answer
 * @property integer $review
 * @property integer $date
 */
class QuestionAll extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'questionall';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'text', 'date'], 'required'],
            [['text'], 'string'],
            [['date'], 'integer'],
            [['review'], 'integer'],
            [['title'], 'string', 'max' => 256],
            [['answer'], 'string'],
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
            'text' => 'Text',
            'answer'=> 'Answer',
            'review'=> 'Review',
            'date' => 'Date',
        ];
    }
}
