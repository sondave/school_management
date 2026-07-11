<?php

use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\SchoolInfo $model */
?>

<div class="school-info-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'school_type',
            'phone_number',
            'email:email',
            'website:url',
            'county',
            'physical_address',
            'postal_address',
            'motto',
            'mission:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
        ],
    ]) ?>
</div>
