<?php

/** @var yii\web\View $this */
/** @var app\models\settings\FeesCategory $model */

$this->title = 'Create Fee Category';
$this->params['breadcrumbs'][] = ['label' => 'Fees Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', ['model' => $model]) ?>
