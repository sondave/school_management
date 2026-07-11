<?php

/** @var yii\web\View $this */
/** @var app\models\settings\Subject $model */

$this->title = 'Update Subject';
$this->params['breadcrumbs'][] = ['label' => 'Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
