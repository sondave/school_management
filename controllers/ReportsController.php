<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\fees\FeePayment;
use app\models\fees\StudentFeeCharge;
use app\models\settings\AcademicYear;
use app\models\settings\Exam;
use app\models\settings\Grade;
use app\models\settings\Term;
use app\models\StudentEnrollment;
use Yii;
use yii\web\Controller;

class ReportsController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index', [
            'kpis' => $this->buildReportKpis(),
        ]);
    }

    public function actionStudents(int $academicYearId = 0, int $termId = 0): string
    {
        $query = StudentEnrollment::find()
            ->alias('en')
            ->select([
                'g.id AS grade_id',
                'g.code AS grade_code',
                'g.grade AS grade_name',
                'COUNT(DISTINCT en.student_id) AS students_count',
            ])
            ->innerJoin(['g' => Grade::tableName()], 'g.id = en.grade_id')
            ->groupBy(['g.id', 'g.code', 'g.grade'])
            ->orderBy(['g.grade' => SORT_ASC])
            ->asArray();

        if ($academicYearId > 0) {
            $query->andWhere(['en.academic_year_id' => $academicYearId]);
        }

        if ($termId > 0) {
            $query->andWhere(['en.term_id' => $termId]);
        }

        $rows = $query->all();
        $totalStudents = 0;
        foreach ($rows as $row) {
            $totalStudents += (int) ($row['students_count'] ?? 0);
        }

        return $this->render('students', [
            'rows' => $rows,
            'totalStudents' => $totalStudents,
            'academicYearId' => $academicYearId,
            'termId' => $termId,
            'academicYearOptions' => AcademicYear::find()
                ->select(['year', 'id'])
                ->orderBy(['year' => SORT_DESC])
                ->indexBy('id')
                ->column(),
            'termOptions' => Term::find()
                ->select(['name', 'id'])
                ->orderBy(['id' => SORT_ASC])
                ->indexBy('id')
                ->column(),
        ]);
    }

    public function actionFees(): string
    {
        $totalBilled = (float) (StudentFeeCharge::find()->sum('amount') ?? 0);
        $totalCollected = (float) (FeePayment::find()->sum('amount') ?? 0);
        $totalOutstanding = (float) (StudentFeeCharge::find()->sum('balance') ?? 0);
        $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0.0;

        $topBalances = Yii::$app->db->createCommand(
            'SELECT st.id AS student_id, st.upi, st.first_name, st.middle_name, st.surname, SUM(fc.balance) AS total_balance
             FROM st_student_fee_charges fc
             INNER JOIN st_students st ON st.id = fc.student_id
             GROUP BY st.id, st.upi, st.first_name, st.middle_name, st.surname
             HAVING SUM(fc.balance) > 0
             ORDER BY total_balance DESC
             LIMIT 10'
        )->queryAll();

        $recentPayments = FeePayment::find()
            ->with(['student'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('fees', [
            'totalBilled' => $totalBilled,
            'totalCollected' => $totalCollected,
            'totalOutstanding' => $totalOutstanding,
            'collectionRate' => $collectionRate,
            'topBalances' => $topBalances,
            'recentPayments' => $recentPayments,
        ]);
    }

    public function actionExams(): string
    {
        $statusCounts = Exam::find()
            ->select(['status', 'COUNT(*) AS total'])
            ->groupBy(['status'])
            ->asArray()
            ->all();

        $statusMap = [
            Exam::STATUS_ACTIVE => 0,
            Exam::STATUS_COMPLETED => 0,
            Exam::STATUS_CANCELED => 0,
        ];

        foreach ($statusCounts as $row) {
            $status = (string) ($row['status'] ?? '');
            if (isset($statusMap[$status])) {
                $statusMap[$status] = (int) ($row['total'] ?? 0);
            }
        }

        $gradeCoverage = Yii::$app->db->createCommand(
            'SELECT g.id AS grade_id, g.code AS grade_code, g.grade AS grade_name, COUNT(DISTINCT eg.exam_id) AS exams_count
             FROM st_grades g
             LEFT JOIN exam_grades eg ON eg.grade_id = g.id
             GROUP BY g.id, g.code, g.grade
             ORDER BY g.grade ASC'
        )->queryAll();

        $recentExams = Exam::find()
            ->with(['academicYear', 'term', 'examType'])
            ->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('exams', [
            'statusMap' => $statusMap,
            'gradeCoverage' => $gradeCoverage,
            'recentExams' => $recentExams,
        ]);
    }

    private function buildReportKpis(): array
    {
        $totalStudents = (int) (StudentEnrollment::find()->select('student_id')->distinct()->count('student_id'));
        $activeExams = (int) Exam::find()->where(['status' => Exam::STATUS_ACTIVE])->count();
        $totalBilled = (float) (StudentFeeCharge::find()->sum('amount') ?? 0);
        $totalCollected = (float) (FeePayment::find()->sum('amount') ?? 0);
        $totalOutstanding = (float) (StudentFeeCharge::find()->sum('balance') ?? 0);

        return [
            'totalStudents' => $totalStudents,
            'activeExams' => $activeExams,
            'totalBilled' => $totalBilled,
            'totalCollected' => $totalCollected,
            'totalOutstanding' => $totalOutstanding,
        ];
    }
}
