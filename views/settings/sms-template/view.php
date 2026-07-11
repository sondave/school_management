<?php

use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\settings\SmsTemplate $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'SMS Templates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sms-template-view modal-table">
    <?= DetailView::widget([
        'model' => $model,
        // 🌟 Add this section to configure the <table> tag styles
        'options' => [
            'class' => 'table table-striped table-bordered detail-view',
            'style' => 'table-layout: fixed; width: 100%;'
        ],
        'attributes' => [
            'name',
            'description',
            // 🌟 Use the 'raw' format array layout to inject custom styles on the template cell
            [
                'attribute' => 'template',
                'format' => 'ntext',
                'contentOptions' => [
                    'style' => 'white-space: normal !important; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;'
                ],
            ],
            [
                'attribute' => 'status',
                'value' => $model->status ? 'Active' : 'Inactive',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Created By',
                'value' => function ($model) {
                    return $model->createdByUser->username ?? 'System';
                },
            ],
        ],
    ]) ?>
</div>


<?php
    // 🌟 Restrict the left column header labels so they stay compact (e.g., 25% wide)
    $this->registerCss("
        .sms-template-view .detail-view th {
            width: 25% !important;
        }
    ");
?>
