<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $analytics */

$this->title = 'Dashboard';

$analytics = $analytics ?? [];
$totalStudents = (int) ($analytics['totalStudents'] ?? 0);
$totalParents = (int) ($analytics['totalParents'] ?? 0);
$activeExams = (int) ($analytics['activeExams'] ?? 0);
$dailyCollections = (float) ($analytics['dailyCollections'] ?? 0);
$monthCollections = (float) ($analytics['monthCollections'] ?? 0);
$outstandingFees = (float) ($analytics['outstandingFees'] ?? 0);
$collectionRate = (float) ($analytics['collectionRate'] ?? 0);

$quickActions = [
    ['label' => 'Add Student', 'url' => ['students/create'], 'icon' => 'users'],
    ['label' => 'Record Fee Payment', 'url' => ['settings/fee-payments/index'], 'icon' => 'credit-card'],
    ['label' => 'Create Exam', 'url' => ['exams/create'], 'icon' => 'file-plus'],
    ['label' => 'Submit Marks', 'url' => ['exams/submit-marks'], 'icon' => 'edit-3'],
];

$moduleLinks = [
    ['title' => 'Students', 'desc' => 'Admissions, profiles, and enrollments.', 'url' => ['students/index'], 'accent' => 'students'],
    ['title' => 'Parents', 'desc' => 'Parent contacts and linked learners.', 'url' => ['parents/index'], 'accent' => 'parents'],
    ['title' => 'School Fees', 'desc' => 'Fee structures, charges, and payments.', 'url' => ['settings/fees-structures/index'], 'accent' => 'fees'],
    ['title' => 'Exams', 'desc' => 'Exam setup, grades, subjects, and marks.', 'url' => ['exams/index'], 'accent' => 'exams'],
    ['title' => 'Reports', 'desc' => 'Portfolio and repayment reports.', 'url' => ['reports/index'], 'accent' => 'reports'],
    ['title' => 'Settings', 'desc' => 'Master data and configuration.', 'url' => ['settings/school-info/index'], 'accent' => 'settings'],
];

$this->registerCss(<<<'CSS'
.landing-wrap {
    display: grid;
    gap: 1.25rem;
}

.landing-hero {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    padding: 1.5rem;
    color: #1f2a37;
    background:
        radial-gradient(120% 140% at 85% 15%, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0) 55%),
        radial-gradient(110% 140% at 0% 100%, rgba(59, 130, 246, 0.2), rgba(59, 130, 246, 0) 50%),
        linear-gradient(120deg, #f3f9ff 0%, #fdfefe 45%, #f4fff7 100%);
    border: 1px solid #e5eef8;
}

.landing-hero h2 {
    margin-bottom: .35rem;
    font-weight: 700;
    font-size: 1.5rem;
}

.landing-hero p {
    margin-bottom: 0;
    max-width: 760px;
    color: #4b5563;
}

.landing-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: 1rem;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: .85rem;
}

.kpi-card {
    position: relative;
    grid-column: span 12;
    border: 1px solid #e6edf5;
    border-radius: 12px;
    background: #ffffff;
    padding: .95rem;
    overflow: hidden;
    transition: transform .18s ease, box-shadow .18s ease;
}

.kpi-card::after {
    content: '';
    position: absolute;
    top: -40px;
    right: -30px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    opacity: .28;
}

.kpi-card h6 {
    margin: 0;
    color: #374151;
    font-size: .84rem;
    font-weight: 700;
}

.kpi-value {
    margin-top: .35rem;
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
}

.kpi-meta {
    margin-top: .25rem;
    color: #475569;
    font-size: .8rem;
}

.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
}

.kpi-card.students {
    border-color: #c8e8ff;
    background: linear-gradient(145deg, #f4fbff 0%, #ffffff 70%);
}

.kpi-card.students::after {
    background: #7dd3fc;
}

.kpi-card.parents {
    border-color: #bfeee8;
    background: linear-gradient(145deg, #f4fffd 0%, #ffffff 70%);
}

.kpi-card.parents::after {
    background: #5eead4;
}

.kpi-card.exams {
    border-color: #fcd8c8;
    background: linear-gradient(145deg, #fff7f4 0%, #ffffff 70%);
}

.kpi-card.exams::after {
    background: #fda4af;
}

.kpi-card.collection {
    border-color: #ddd6fe;
    background: linear-gradient(145deg, #faf7ff 0%, #ffffff 70%);
}

.kpi-card.collection::after {
    background: #c4b5fd;
}

.quick-actions-card,
.module-card,
.insight-card {
    border: 1px solid #e6edf5;
    border-radius: 12px;
    background: #ffffff;
}

.quick-actions-card,
.insight-card {
    padding: 1rem;
}

.quick-actions-card {
    grid-column: span 12;
}

.insight-card {
    grid-column: span 12;
}

.module-list {
    grid-column: span 12;
    display: grid;
    gap: .85rem;
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

.action-grid {
    display: grid;
    gap: .65rem;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.action-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 10px;
    border: 1px solid #dbe5f1;
    padding: .7rem .85rem;
    color: #1f2a37;
    text-decoration: none;
    transition: all .18s ease;
    background: #fbfdff;
}

.action-link:hover {
    border-color: #93c5fd;
    background: #f0f7ff;
    transform: translateY(-1px);
}

.module-card {
    padding: 1rem;
    text-decoration: none;
    color: #111827;
    transition: all .2s ease;
}

.module-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(17, 24, 39, 0.08);
}

.module-card h6 {
    margin-bottom: .3rem;
    font-weight: 700;
}

.module-card p {
    margin: 0;
    color: #6b7280;
    font-size: .92rem;
}

.module-card.students { border-left: 4px solid #0ea5e9; }
.module-card.parents { border-left: 4px solid #14b8a6; }
.module-card.fees { border-left: 4px solid #f59e0b; }
.module-card.exams { border-left: 4px solid #ef4444; }
.module-card.reports { border-left: 4px solid #8b5cf6; }
.module-card.settings { border-left: 4px solid #64748b; }

.insight-stat {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: .6rem;
    margin-top: .85rem;
}

.stat-chip {
    border-radius: 10px;
    background: #f8fafc;
    border: 1px dashed #d7e0eb;
    padding: .7rem;
    text-align: center;
}

.stat-chip strong {
    display: block;
    color: #111827;
}

.stat-chip span {
    color: #6b7280;
    font-size: .82rem;
}

@media (min-width: 992px) {
    .kpi-card { grid-column: span 3; }
    .quick-actions-card { grid-column: span 4; }
    .insight-card { grid-column: span 8; }
    .module-list {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
CSS);
?>

<div class="landing-wrap">
    <section class="landing-hero">
        <h2>School Management Hub</h2>
        <p>Welcome to your operations center. Start from a quick action, continue with module workflows, and keep academic data in sync across fees, exams, and student records.</p>
    </section>

    <section class="landing-grid">
        <div class="analytics-grid" style="grid-column: span 12;">
            <div class="kpi-card students">
                <h6>Total Students</h6>
                <div class="kpi-value"><?= number_format($totalStudents) ?></div>
                <div class="kpi-meta">Registered learner records</div>
            </div>
            <div class="kpi-card parents">
                <h6>Total Parents</h6>
                <div class="kpi-value"><?= number_format($totalParents) ?></div>
                <div class="kpi-meta">Parent contacts in system</div>
            </div>
            <div class="kpi-card exams">
                <h6>Active Exams</h6>
                <div class="kpi-value"><?= number_format($activeExams) ?></div>
                <div class="kpi-meta">Exams currently in active status</div>
            </div>
            <div class="kpi-card collection">
                <h6>Collection Rate</h6>
                <div class="kpi-value"><?= number_format($collectionRate, 1) ?>%</div>
                <div class="kpi-meta">Collected vs billed fees</div>
            </div>
        </div>

        <div class="quick-actions-card">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="action-grid">
                <?php foreach ($quickActions as $action): ?>
                    <a class="action-link" href="<?= \yii\helpers\Url::to($action['url']) ?>">
                        <span><?= Html::encode($action['label']) ?></span>
                        <i data-feather="<?= Html::encode($action['icon']) ?>" class="feather-16"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="insight-card">
            <h5 class="mb-2">Operational Focus</h5>
            <p class="mb-0 text-muted">Track the three daily priorities below to keep admissions, billing, and assessments moving without gaps.</p>
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2">
                    <span class="text-muted">Today's Collections</span>
                    <strong>KES <?= number_format($dailyCollections, 2) ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2">
                    <span class="text-muted">This Month's Collections</span>
                    <strong>KES <?= number_format($monthCollections, 2) ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2">
                    <span class="text-muted">Outstanding Fee Balance</span>
                    <strong class="text-danger">KES <?= number_format($outstandingFees, 2) ?></strong>
                </div>
            </div>
            <div class="insight-stat">
                <div class="stat-chip">
                    <strong>Enrollment</strong>
                    <span>Update grade + term placement first</span>
                </div>
                <div class="stat-chip">
                    <strong>Finance</strong>
                    <span>Post collections and monitor balances</span>
                </div>
                <div class="stat-chip">
                    <strong>Assessment</strong>
                    <span>Allocate exam grades before mark entry</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <h5 class="mb-3">Modules</h5>
        <div class="module-list">
            <?php foreach ($moduleLinks as $item): ?>
                <a class="module-card <?= Html::encode($item['accent']) ?>" href="<?= \yii\helpers\Url::to($item['url']) ?>">
                    <h6><?= Html::encode($item['title']) ?></h6>
                    <p><?= Html::encode($item['desc']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>



