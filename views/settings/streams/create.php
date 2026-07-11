<?php

/** @var yii\web\View $this */
/** @var app\models\settings\Stream $model */

$this->title = 'Create Stream';
$this->params['breadcrumbs'][] = ['label' => 'Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
