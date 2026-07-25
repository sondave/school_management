<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\web\Controller as BaseController;

class Controller extends BaseController
{
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (in_array((string) $action->id, ['login', 'error', 'captcha'], true)) {
            return true;
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
            return false;
        }

        return true;
    }
}
