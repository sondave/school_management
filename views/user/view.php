<?php

use app\models\User;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\UserProfile|null $profile */

$this->title = 'User Details';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <div class="page-header">
        <div class="page-title">
            <h4><?= Html::encode($this->title) ?></h4>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= Yii::$app->homeUrl ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to(['index']) ?>">Users</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                <div>
                    <h5 class="mb-1">User Profile Summary</h5>
                    <p class="text-muted mb-0">A clear overview of the account details and contact information.</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <?= Html::a('Back to Users', ['index'], ['class' => 'btn btn-secondary']) ?>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted mb-3">Account Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <div class="text-dark"><?= Html::encode((string) $user->username) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <div class="text-dark"><?= Html::encode((string) $user->email) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <div><?= Html::tag('span', Html::encode($user->getStatusLabel()), ['class' => 'badge bg-success']) ?></div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Last Login</label>
                            <div class="text-dark"><?= Html::encode((string) ($user->last_login_at ?: '-')) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-muted mb-3">Personal Information</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <div class="text-dark"><?= Html::encode(trim((string) (($profile?->first_name ?? '') . ' ' . ($profile?->other_names ?? '')))) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <div class="text-dark"><?= Html::encode((string) ($profile?->phone ?? '-')) ?></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gender</label>
                            <div class="text-dark"><?= Html::encode((string) ($profile?->gender ?? '-')) ?></div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">Date of Birth</label>
                            <div class="text-dark"><?= Html::encode((string) ($profile?->dob ?? '-')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
