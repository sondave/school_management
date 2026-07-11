<?php

/** @var yii\web\View $this */
/** @var app\models\Parents $model */

$this->title = 'Update Parent';
$this->params['breadcrumbs'][] = ['label' => 'Parents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
