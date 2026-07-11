<?php

/** @var yii\web\View $this */
/** @var app\models\ClassTeacher $model */

$this->title = 'Create Class Teacher';
$this->params['breadcrumbs'][] = ['label' => 'Class Teachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
