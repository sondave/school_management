<?php

/** @var yii\web\View $this */
/** @var app\models\settings\Exam $model */

$this->title = 'Update Exam';
$this->params['breadcrumbs'][] = ['label' => 'Exams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>
