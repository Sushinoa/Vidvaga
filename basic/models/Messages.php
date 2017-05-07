<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property integer $id
 * @property string $sms_text
 * @property string $email_text
 * @property string $filter_list
 * @property integer $date
 */
class Messages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'integer'],
            [['date'], 'default',  'value'=> time()],
            [['sms_text', 'email_text', 'filter_list'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sms_text' => 'Sms Text',
            'email_text' => 'Email Text',
            'filter_list' => 'Filter List',
            'date' => 'Date',
        ];
    }
}
