<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);//debug On
defined('YII_ENV') or define('YII_ENV', 'dev');//develop version On

require(__DIR__ . '/../vendor/autoload.php');//class autoload
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');//lib app

$config = require(__DIR__ . '/../config/web.php');//global array settings

(new yii\web\Application($config))->run();//create object web application
