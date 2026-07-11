<?php

/** @var yii\web\View $this */
/** @var app\models\settings\LookupValue $model */

$this->title = 'Update Lookup Value';
$this->params['breadcrumbs'][] = ['label' => 'Lookup Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
