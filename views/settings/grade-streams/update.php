<?php

/** @var yii\web\View $this */
/** @var app\models\settings\GradeStream $model */

$this->title = 'Update Grade Stream';
$this->params['breadcrumbs'][] = ['label' => 'Grade Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
