<?php

/** @var yii\web\View $this */
/** @var app\models\settings\SchoolInfo $model */

$this->title = 'Update School Info';
$this->params['breadcrumbs'][] = ['label' => 'School Info', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
