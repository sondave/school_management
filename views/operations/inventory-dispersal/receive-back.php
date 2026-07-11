<?php

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryDispersal $model */

$this->title = 'Receive Back';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Dispersal', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-dispersal-receive-back">
    <?= $this->render('_receive_back_form', [
        'model' => $model,
    ]) ?>
</div>