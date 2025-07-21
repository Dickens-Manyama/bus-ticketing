<?php
/** @var yii\web\View $this */
use yii\helpers\Html;

$this->title = 'Reports Dashboard';
?>
<style>
.fade-in { animation: fadeIn 1s; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.report-card { transition: transform 0.3s, box-shadow 0.3s; }
.report-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="bi bi-file-earmark-text text-primary"></i> Reports Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print All
                </button>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row fade-in">
        <!-- Super Admin Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0"><i class="bi bi-shield-check"></i> Super Admin Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Comprehensive system overview including user statistics, booking analytics, revenue reports, and system health metrics.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> User distribution by role</li>
                        <li><i class="bi bi-check-circle text-success"></i> Booking status analytics</li>
                        <li><i class="bi bi-check-circle text-success"></i> Revenue and financial data</li>
                        <li><i class="bi bi-check-circle text-success"></i> Ticket verification statistics</li>
                        <li><i class="bi bi-check-circle text-success"></i> System performance metrics</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Admin Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Business-focused report with revenue trends, booking analytics, route performance, and customer insights.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Monthly revenue trends</li>
                        <li><i class="bi bi-check-circle text-success"></i> Top performing routes</li>
                        <li><i class="bi bi-check-circle text-success"></i> Booking status breakdown</li>
                        <li><i class="bi bi-check-circle text-success"></i> Ticket verification metrics</li>
                        <li><i class="bi bi-check-circle text-success"></i> Business performance insights</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Bookings Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-ticket-detailed"></i> Bookings Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Detailed analysis of booking patterns, status distribution, and recent booking activities.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Booking status breakdown</li>
                        <li><i class="bi bi-check-circle text-success"></i> Recent booking history</li>
                        <li><i class="bi bi-check-circle text-success"></i> Route performance analysis</li>
                        <li><i class="bi bi-check-circle text-success"></i> Booking trends and patterns</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/bookings'])) ?>" class="btn btn-success w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Revenue Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Financial analysis including revenue by route, monthly trends, and profit margins.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Revenue by route analysis</li>
                        <li><i class="bi bi-check-circle text-success"></i> Monthly revenue trends</li>
                        <li><i class="bi bi-check-circle text-success"></i> Profit margin calculations</li>
                        <li><i class="bi bi-check-circle text-success"></i> Financial performance metrics</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/revenue'])) ?>" class="btn btn-warning w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Fleet Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bus-front"></i> Fleet Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Fleet management analysis including bus utilization, maintenance schedules, and performance metrics.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Bus utilization rates</li>
                        <li><i class="bi bi-check-circle text-success"></i> Maintenance schedules</li>
                        <li><i class="bi bi-check-circle text-success"></i> Fleet performance metrics</li>
                        <li><i class="bi bi-check-circle text-success"></i> Capacity analysis</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/fleet'])) ?>" class="btn btn-info w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Route Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Route Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Route performance analysis including popularity, profitability, and operational efficiency.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> Route popularity analysis</li>
                        <li><i class="bi bi-check-circle text-success"></i> Route profitability</li>
                        <li><i class="bi bi-check-circle text-success"></i> Operational efficiency</li>
                        <li><i class="bi bi-check-circle text-success"></i> Route optimization insights</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/route'])) ?>" class="btn btn-secondary w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Users Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Users Report</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">User analytics including registration trends, role distribution, and user activity patterns.</p>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success"></i> User registration trends</li>
                        <li><i class="bi bi-check-circle text-success"></i> Role distribution analysis</li>
                        <li><i class="bi bi-check-circle text-success"></i> User activity patterns</li>
                        <li><i class="bi bi-check-circle text-success"></i> User engagement metrics</li>
                    </ul>
                </div>
                <div class="card-footer bg-light">
                    <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/users'])) ?>" class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4 fade-in">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/super-admin'])) ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-shield-check"></i> Super Admin Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/admin'])) ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-badge"></i> Admin Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/bookings'])) ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-ticket-detailed"></i> Bookings Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/reports/revenue'])) ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-cash-coin"></i> Revenue Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= Html::encode(Yii::$app->urlManager->createUrl(['/dashboard/index'])) ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 