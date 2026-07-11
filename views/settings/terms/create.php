<?php

/** @var yii\web\View $this */
/** @var app\models\settings\Term $model */

$this->title = 'Create Term';
$this->params['breadcrumbs'][] = ['label' => 'Terms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
