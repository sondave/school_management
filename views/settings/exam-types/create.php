<?php

/** @var yii\web\View $this */
/** @var app\models\settings\ExamType $model */

$this->title = 'Create Exam Type';
$this->params['breadcrumbs'][] = ['label' => 'Exam Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>
