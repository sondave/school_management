<?php

declare(strict_types=1);

namespace app\controllers\operations;

use app\models\operations\StockLevel;
use yii\data\ActiveDataProvider;
use app\controllers\Controller;

class StockLevelsController extends Controller
{
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => StockLevel::find()
                ->with(['inventoryItem'])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}