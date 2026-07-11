<?php

/** @var yii\web\View $this */
/** @var app\models\TeacherSubject $model */

$this->title = 'Update Teacher Subjects';
$this->params['breadcrumbs'][] = ['label' => 'Teacher Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>