<?php

use app\models\operations\StockLevel;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Stock Levels';
$this->params['breadcrumbs'][] = 'Operations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-levels-index">
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4><?= Html::encode($this->title) ?></h4>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['/']) ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($this->title) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'stock-level-grid-pjax', 'timeout' => 5000]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'emptyText' => 'No stock level records found.',
                'tableOptions' => [
                    'class' => 'table datanew no-footer table-hover',
                ],
                'layout' => "<div class='table-responsive custom-table-card'>\n{items}\n</div>\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}\n{pager}</div>",
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'inventory_item_id',
                        'value' => static fn(StockLevel $model): string => $model->inventoryItem?->name ?? '-',
                    ],
                    [
                        'label' => 'Accessory Type',
                        'value' => static fn(StockLevel $model): string => $model->inventoryItem?->getAccessoryTypeLabel() ?? '-',
                    ],
                    'total_received',
                    'total_issued',
                    'total_returned',
                    'updated_at:datetime',
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>