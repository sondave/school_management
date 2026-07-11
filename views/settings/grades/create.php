<?php

/** @var yii\web\View $this */
/** @var app\models\settings\Grade $model */

$this->title = 'Create Grade';
$this->params['breadcrumbs'][] = ['label' => 'Grades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
