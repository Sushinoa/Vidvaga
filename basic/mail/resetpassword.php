<?php

/**
 * Created by PhpStorm.
 * User: web
 * Date: 22.03.2017
 * Time: 11:47
 *
 * @var $user \app\models\Users
 */

use yii\helpers\Html;


echo 'Привет'.Html::encode($user->phone_number).'.';
echo Html::a('Для смены пароля перейдите по этой ссылке.',
    Yii::$app->urlManager->createAbsoluteUrl(
        [
            '/api/v1/users/reset',//url который отдаст фронт на форму сброса пароля
            'key'=>$user->secret_key //как передать секретный ключ обратно на сервер??? передаь в юзерконтроллер в событие ресет
        ]
    )
);