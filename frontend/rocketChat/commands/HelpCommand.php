<?php

namespace frontend\rocketChat\commands;

use frontend\rocketChat\models\Request;
use yii\base\Object;

class HelpCommand extends Object implements Command
{
    public static function supports($text)
    {
        return stripos($text, 'help') === 0;
    }

    public function execute(Request $request)
    {
        return \Yii::$app->view->render('/rocket-chat/commands/help');
    }
}