<?php

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryDispersal $model */

$this->title = 'Update Inventory Dispersal';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Dispersal', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-dispersal-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>