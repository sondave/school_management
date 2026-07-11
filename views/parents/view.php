<?php

use app\models\Parents;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Parents $model */
?>

<div class="parent-view">
    <h6 class="mb-3">Parent Profile</h6>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'first_name',
            'other_names',
            [
                'attribute' => 'gender',
                'value' => $model->getGenderLabel(),
            ],
            'national_id',
            'date_of_birth:date',
            'phone_no',
            'alternate_phone_no',
            'email:email',
            [
                'attribute' => 'county',
                'value' => $model->getCountyLabel(),
            ],
            'physical_address',
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'created_by',
                'value' => static function (Parents $model): string {
                    return $model->createdByUser?->username ?? '-';
                },
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'updated_by',
                'value' => static function (Parents $model): string {
                    return $model->updatedByUser?->username ?? '-';
                },
            ],
        ],
    ]) ?>

    <div class="mt-4">
        <h6 class="mb-2">Linked Students</h6>
        <?php if (!empty($model->studentParents)) : ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Student</th>
                            <th>Relationship</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->studentParents as $index => $link) : ?>
                            <tr>
                                <td><?= (int) $index + 1 ?></td>
                                <td>
                                    <?php if ($link->student !== null) : ?>
                                        <?= Html::a(
                                            Html::encode($link->student->getFullName()),
                                            Url::to(['students/profile', 'id' => $link->student_id]),
                                            ['target' => '_blank', 'rel' => 'noopener']
                                        ) ?>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode($link->getRelationshipLabel()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="text-muted">No students are currently linked to this parent.</div>
        <?php endif; ?>
    </div>
</div>
