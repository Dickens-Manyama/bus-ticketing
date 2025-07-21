<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $role string */
/* @var $isSuperAdmin bool */
/* @var $isAdmin bool */
/* @var $isManager bool */
/* @var $isStaff bool */

$this->title = 'Dashboard Test';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="bi bi-bug"></i> Dashboard Role Test</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Current User Information:</h5>
                        <ul>
                            <li><strong>Username:</strong> <?= Html::encode($user->username) ?></li>
                            <li><strong>Email:</strong> <?= Html::encode($user->email) ?></li>
                            <li><strong>Role:</strong> <span class="badge bg-primary"><?= Html::encode($role) ?></span></li>
                            <li><strong>User ID:</strong> <?= $user->id ?></li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h5>Role Permissions:</h5>
                        <ul>
                            <li><strong>isSuperAdmin():</strong> 
                                <?php if ($isSuperAdmin): ?>
                                    <span class="badge bg-success">TRUE</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">FALSE</span>
                                <?php endif; ?>
                            </li>
                            <li><strong>isAdmin():</strong> 
                                <?php if ($isAdmin): ?>
                                    <span class="badge bg-success">TRUE</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">FALSE</span>
                                <?php endif; ?>
                            </li>
                            <li><strong>isManager():</strong> 
                                <?php if ($isManager): ?>
                                    <span class="badge bg-success">TRUE</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">FALSE</span>
                                <?php endif; ?>
                            </li>
                            <li><strong>isStaff():</strong> 
                                <?php if ($isStaff): ?>
                                    <span class="badge bg-success">TRUE</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">FALSE</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h5>Expected Dashboard:</h5>
                        <?php
                        $expectedDashboard = '';
                        switch ($role) {
                            case 'superadmin':
                                $expectedDashboard = 'Super Admin Dashboard (Full system access with all analytics)';
                                break;
                            case 'admin':
                                $expectedDashboard = 'Admin Dashboard (Business-focused with revenue tracking)';
                                break;
                            case 'manager':
                                $expectedDashboard = 'Manager Dashboard (Operational metrics and route performance)';
                                break;
                            case 'staff':
                                $expectedDashboard = 'Staff Dashboard (Basic operations and daily tasks)';
                                break;
                            default:
                                $expectedDashboard = 'Staff Dashboard (Default fallback)';
                        }
                        ?>
                        <p><strong><?= Html::encode($expectedDashboard) ?></strong></p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6><i class="bi bi-link"></i> Quick Links</h6>
                                </div>
                                <div class="card-body">
                                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/dashboard/index'])) ?>" class="btn btn-primary mb-2 w-100">
                                        <i class="bi bi-speedometer2"></i> Go to Dashboard
                                    </a>
                                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/site/logout'])) ?>" class="btn btn-secondary mb-2 w-100">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6><i class="bi bi-info-circle"></i> Test Instructions</h6>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Click "Go to Dashboard" to see your role-specific dashboard</li>
                                        <li>Verify that the dashboard matches your role</li>
                                        <li>Check that the navigation shows appropriate menu items</li>
                                        <li>Test different user roles by logging in with different accounts</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light mt-3">
                        <h6>Available Test Accounts:</h6>
                        <ul>
                            <li><strong>superadmin</strong> / Admin@123 - Super Admin Dashboard</li>
                            <li><strong>admin</strong> / Admin@123 - Admin Dashboard</li>
                            <li><strong>manager</strong> / Admin@123 - Manager Dashboard</li>
                            <li><strong>staff</strong> / Admin@123 - Staff Dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 