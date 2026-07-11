<?php

/** @var yii\web\View $this */
/** @var app\models\operations\InventoryItem $model */

$this->title = 'Update Inventory Item';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>