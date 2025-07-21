<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $bookings common\models\Booking[] */
/* @var $staffUsers common\models\User[] */

$this->title = 'Staff Activity';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Map staff IDs to usernames, status, and last login for quick lookup
$staffMap = [];
$staffStatusMap = [];
$staffLastLoginMap = [];
foreach ($staffUsers as $staff) {
    $staffMap[$staff->id] = $staff->username;
    $staffStatusMap[$staff->id] = $staff->status;
    $staffLastLoginMap[$staff->id] = $staff->last_login;
}
?>
<div class="staff-activity">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-person-lines-fill"></i> Staff Booking Activity</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Staff Member</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Booking ID</th>
                        <th>Route</th>
                        <th>Passenger</th>
                        <th>Date/Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $i => $booking): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($staffMap[$booking->user_id] ?? 'Unknown') ?></td>
                        <td>
                            <?php
                            $status = $staffStatusMap[$booking->user_id] ?? null;
                            if ($status === 10) {
                                echo '<span class="badge bg-success">Active</span>';
                            } elseif ($status === 9) {
                                echo '<span class="badge bg-warning text-dark">Inactive</span>';
                            } elseif ($status === 0) {
                                echo '<span class="badge bg-danger">Deleted</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Unknown</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $lastLogin = $staffLastLoginMap[$booking->user_id] ?? null;
                            if ($lastLogin) {
                                echo Yii::$app->formatter->asDatetime($lastLogin);
                            } else {
                                echo '<span class="text-muted">Never</span>';
                            }
                            ?>
                        </td>
                        <td><?= Html::encode($booking->id) ?></td>
                        <td><?= Html::encode($booking->route ?? '-') ?></td>
                        <td><?= Html::encode($booking->passenger_name ?? '-') ?></td>
                        <td><?= Yii::$app->formatter->asDatetime($booking->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if (empty($bookings)): ?>
                <div class="alert alert-info m-3">No staff activity found.</div>
            <?php endif; ?>
        </div>
    </div>
</div> 