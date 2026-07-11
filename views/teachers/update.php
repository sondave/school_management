<?php

/** @var yii\web\View $this */
/** @var app\models\Teacher $model */

$this->title = 'Update Teacher';
$this->params['breadcrumbs'][] = ['label' => 'Teachers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
