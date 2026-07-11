<?php

/** @var yii\web\View $this */
/** @var app\models\settings\AcademicYear $model */

$this->title = 'Update Academic Year';
$this->params['breadcrumbs'][] = ['label' => 'Academic Years', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
