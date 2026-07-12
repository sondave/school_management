<?php

/** @var yii\web\View $this */
/** @var app\models\settings\FeesStructure $model */

$this->title = 'Create Fee Structure';
$this->params['breadcrumbs'][] = ['label' => 'Fees Structure', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>
