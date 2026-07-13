<?php

use app\models\settings\ExamGrade;
use app\models\settings\Subject;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var ExamGrade $examGrade */
/** @var Subject[] $availableSubjects */
/** @var int[] $selectedSubjectIds */

$selectedMap = array_fill_keys(array_map('intval', $selectedSubjectIds), true);
?>

<div class="exam-grade-subjects-form-wrap">
    <div class="mb-3">
        <strong>Exam:</strong> <?= Html::encode((string) ($examGrade->exam?->name ?? '-')) ?>
        <span class="ms-3"><strong>Grade:</strong> <?= Html::encode((string) ($examGrade->grade?->grade ?? '-')) ?></span>
    </div>

    <?= Html::beginForm(['exams/grade-subjects', 'examGradeId' => (int) $examGrade->id], 'post', ['id' => 'exam-grade-subjects-form']) ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 70px;">Pick</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($availableSubjects)): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No active grade subjects found for this grade.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($availableSubjects as $subject): ?>
                        <tr>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="subject_ids[]"
                                    value="<?= (int) $subject->id ?>"
                                    <?= isset($selectedMap[(int) $subject->id]) ? 'checked' : '' ?>
                                >
                            </td>
                            <td><?= Html::encode((string) $subject->code) ?></td>
                            <td><?= Html::encode((string) $subject->name) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-end gap-2">
        <?= Html::button('Close', ['class' => 'btn btn-light', 'data-bs-dismiss' => 'modal']) ?>
        <?= Html::submitButton('Save Subjects', ['class' => 'btn btn-primary']) ?>
    </div>

    <?= Html::endForm() ?>
</div>
