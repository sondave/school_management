<?php

/** @var yii\web\View $this */
/** @var app\models\operations\Inventory $model */

$this->title = 'Update Inventory Item';
$this->params['breadcrumbs'][] = ['label' => 'Inventory', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>