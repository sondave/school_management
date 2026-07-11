<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Student $model */
/** @var app\models\StudentParent $parentModel */
?>

<div class="student-parents-tab">
    <h5 class="mb-3">Linked Parents</h5>

    <div class="table-responsive mb-4">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Parent</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Relationship</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($model->studentParents)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No linked parents found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($model->studentParents as $link): ?>
                    <tr>
                        <td><?= Html::encode(trim((string) ($link->parent?->first_name . ' ' . $link->parent?->other_names))) ?></td>
                        <td><?= Html::encode((string) ($link->parent?->phone_no ?? '-')) ?></td>
                        <td><?= Html::encode((string) ($link->parent?->email ?? '-')) ?></td>
                        <td><?= Html::encode($link->getRelationshipLabel()) ?></td>
                        <td class="text-end">
                            <?= Html::a('Remove', ['students/remove-parent', 'id' => $model->id, 'parentId' => $link->parent_id], [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Remove this parent from the student?',
                                ],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
