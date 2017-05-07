<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 14.03.2017
 * Time: 13:18
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
/**
 * Инициализатор RBAC выполняется в консоли php yii rbac/init
 */
class RbacController extends Controller {

    public function actionInit() {
        $auth = Yii::$app->authManager;

        $auth->removeAll(); //На всякий случай удаляем старые данные из БД...

        // Создадим роли админа, контент менеджера и юзера
        $admin = $auth->createRole('admin');
        $manager = $auth->createRole('manager');
        $user = $auth->createRole('user');
        // запишем их в БД
        $auth->add($admin);
        $auth->add($manager);
        $auth->add($user);

        // Создаем разрешения. Например, просмотр админки viewAdminPage и редактирование новости updateNews
        $viewAdminPage = $auth->createPermission('update');//указываем экшины ?
        $viewAdminPage->description = 'Редактирование новости';
        $viewNews = $auth->createPermission('view');
        $viewNews->description = 'Просмотр новостей';
        $updateNews = $auth->createPermission('updateNews');
        $updateNews->description = 'Редактирование новости';

        // Запишем эти разрешения в БД
        $auth->add($viewAdminPage);
        $auth->add($updateNews);
        $auth->add($viewNews);
        // Теперь добавим наследования. Для роли manager мы добавим разрешение updateNews,
        // а для admin добавим наследование от роли manager и еще добавим собственное разрешение viewAdminPage
        //а для user добавим наследование от manager
        // Роли «Контент менеджер» присваиваем разрешение «Редактирование новости»
        $auth->addChild($manager,$updateNews);

        // manager наследует роль user.
        $auth->addChild($manager, $user);

        // админ наследует роль manager. Он же admin, должен уметь всё! :D
        $auth->addChild($admin, $manager);

        // Еще админ имеет собственное разрешение - «Просмотр админки»
        $auth->addChild($admin, $viewAdminPage);

        // Usr имееет разрешение просмотр новостей
        $auth->addChild($user, $viewNews);

        // Назначаем роль admin пользователю с ID 1
        $auth->assign($admin, 1);

        // Назначаем роль manager пользователю с ID 2
        $auth->assign($manager, 2);

        // Назначаем роль manager пользователю с ID 0
        $auth->assign($user, 0);

        $this->stdout('Done!'. PHP_EOL);
    }
}