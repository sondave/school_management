<?php

declare(strict_types=1);

namespace app\services;

use app\models\StudentEnrollment;
use app\models\settings\FeesStructure;
use Yii;

class StudentFeeChargeService
{
    /**
     * Creates fee charges for all students enrolled in the fee structure's grade and academic year.
     */
    public static function createChargesForFeeStructure(FeesStructure $feeStructure): void
    {
        $studentIds = StudentEnrollment::find()
            ->select('student_id')
            ->where([
                'grade_id' => (int) $feeStructure->grade_id,
                'academic_year_id' => (int) $feeStructure->academic_year_id,
                'term_id' => (int) $feeStructure->term_id,
            ])
            ->distinct()
            ->column();

        if (empty($studentIds)) {
            return;
        }

        $existingStudentIds = (new \yii\db\Query())
            ->select('student_id')
            ->from('{{%st_student_fee_charges}}')
            ->where([
                'fee_structure_id' => (int) $feeStructure->id,
                'student_id' => $studentIds,
            ])
            ->column();

        $existingMap = array_fill_keys(array_map('intval', $existingStudentIds), true);
        $rows = [];

        foreach ($studentIds as $studentId) {
            $studentId = (int) $studentId;
            if (isset($existingMap[$studentId])) {
                continue;
            }

            $rows[] = [
                $studentId,
                (int) $feeStructure->id,
                (float) $feeStructure->amount,
                0,
            ];
        }

        self::batchInsertRows($rows);
    }

    /**
     * Creates fee charges for a newly created enrollment based on matching grade and academic year fee structures.
     */
    public static function createChargesForEnrollment(StudentEnrollment $enrollment): void
    {
        $feeStructures = FeesStructure::find()
            ->select(['id', 'amount'])
            ->where([
                'grade_id' => (int) $enrollment->grade_id,
                'academic_year_id' => (int) $enrollment->academic_year_id,
                'term_id' => (int) $enrollment->term_id,
                'status' => FeesStructure::STATUS_ACTIVE,
            ])
            ->asArray()
            ->all();

        if (empty($feeStructures)) {
            return;
        }

        $feeStructureIds = array_map(static fn(array $row): int => (int) $row['id'], $feeStructures);

        $existingFeeStructureIds = (new \yii\db\Query())
            ->select('fee_structure_id')
            ->from('{{%st_student_fee_charges}}')
            ->where([
                'student_id' => (int) $enrollment->student_id,
                'fee_structure_id' => $feeStructureIds,
            ])
            ->column();

        $existingMap = array_fill_keys(array_map('intval', $existingFeeStructureIds), true);
        $rows = [];

        foreach ($feeStructures as $feeStructure) {
            $feeStructureId = (int) $feeStructure['id'];
            if (isset($existingMap[$feeStructureId])) {
                continue;
            }

            $rows[] = [
                (int) $enrollment->student_id,
                $feeStructureId,
                (float) $feeStructure['amount'],
                0,
            ];
        }

        self::batchInsertRows($rows);
    }

    /**
     * @param array<int, array{0:int,1:int,2:float,3:int}> $rows
     */
    private static function batchInsertRows(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        Yii::$app->db->createCommand()->batchInsert(
            '{{%st_student_fee_charges}}',
            ['student_id', 'fee_structure_id', 'amount', 'discount'],
            $rows
        )->execute();
    }
}
