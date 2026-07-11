<?php

/** @var yii\web\View $this */
/** @var app\models\settings\GradeSubject $model */

$this->title = 'Update Grade Subject';
$this->params['breadcrumbs'][] = ['label' => 'Grade Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
