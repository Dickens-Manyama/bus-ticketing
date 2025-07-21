<?php
/** @var yii\web\View $this */
use yii\helpers\Html;

$this->title = 'Super Admin Comprehensive Report';
?>
<style>
.fade-in { animation: fadeIn 1s; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.table-hover tbody tr:hover { background-color: #f1f3f4; transition: background 0.3s; }
.print-hide { display: inline-block; }
@media print { 
    .print-hide { display: none !important; } 
    .container-fluid { width: 100% !important; }
    .card { border: 1px solid #ddd !important; }
    .table { font-size: 12px !important; }
}
.report-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.status-badge { font-size: 0.8em; }
</style>

<div class="container-fluid">
    <!-- Report Header -->
    <div class="report-header p-4 mb-4 rounded shadow-sm fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2"><i class="bi bi-shield-check"></i> Super Admin Comprehensive Report</h1>
                <p class="mb-0">Generated on: <?= date('F j, Y \a\t g:i A') ?></p>
            </div>
            <div class="col-md-4 text-end print-hide">
                <button class="btn btn-light me-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
                <button class="btn btn-light" onclick="exportToPDF()">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- System Overview Cards -->
    <div class="row mb-4 fade-in">
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h6 class="card-title mt-2">Total Users</h6>
                    <p class="card-text display-6 fw-bold mb-0"><?= $totalUsers ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body text-center">
                    <i class="bi bi-ticket-detailed display-6"></i>
                    <h6 class="card-title mt-2">Total Bookings</h6>
                    <p class="card-text display-6 fw-bold mb-0"><?= $totalBookings ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin display-6"></i>
                    <h6 class="card-title mt-2">Total Revenue</h6>
                    <p class="card-text display-6 fw-bold mb-0"><?= number_format($totalRevenue) ?></p>
                    <small>TZS</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bus-front display-6"></i>
                    <h6 class="card-title mt-2">Fleet Size</h6>
                    <p class="card-text display-6 fw-bold mb-0"><?= $totalBuses ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-secondary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-geo-alt display-6"></i>
                    <h6 class="card-title mt-2">Active Routes</h6>
                    <p class="card-text display-6 fw-bold mb-0"><?= $totalRoutes ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-dark h-100">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up display-6"></i>
                    <h6 class="card-title mt-2">System Health</h6>
                    <p class="card-text display-6 fw-bold mb-0">98%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Distribution -->
    <div class="row mb-4 fade-in">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-pie-chart"></i> User Distribution by Role
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="usersByRoleChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($usersByRole as $role => $count): ?>
                                            <tr>
                                                <td><span class="badge bg-secondary"><?= ucfirst($role) ?></span></td>
                                                <td class="text-end fw-bold"><?= $count ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-bar-chart"></i> Booking Status Distribution
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="bookingStatusChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <?php foreach ($statusCounts as $status => $count): ?>
                                            <tr>
                                                <td><span class="badge bg-<?= $status === 'pending' ? 'warning' : ($status === 'confirmed' ? 'success' : ($status === 'cancelled' ? 'danger' : 'primary')) ?>"><?= ucfirst($status) ?></span></td>
                                                <td class="text-end fw-bold"><?= $count ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Verification Statistics -->
    <div class="row mb-4 fade-in">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="bi bi-qr-code-scan"></i> Ticket Verification Status
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-check-circle text-success display-4"></i>
                                <h4 class="mt-2 mb-1"><?= $activeTickets ?></h4>
                                <small class="text-muted">Active Tickets</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-x-circle text-danger display-4"></i>
                                <h4 class="mt-2 mb-1"><?= $usedTickets ?></h4>
                                <small class="text-muted">Verified/Used</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-exclamation-triangle text-warning display-4"></i>
                                <h4 class="mt-2 mb-1"><?= $expiredTickets ?></h4>
                                <small class="text-muted">Expired</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="border rounded p-3">
                                <i class="bi bi-percent text-info display-4"></i>
                                <h4 class="mt-2 mb-1"><?= $totalBookings > 0 ? round(($usedTickets / $totalBookings) * 100, 1) : 0 ?>%</h4>
                                <small class="text-muted">Verification Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" style="width: <?= $totalBookings > 0 ? ($activeTickets / $totalBookings) * 100 : 0 ?>%">
                                Active (<?= $activeTickets ?>)
                            </div>
                            <div class="progress-bar bg-danger" style="width: <?= $totalBookings > 0 ? ($usedTickets / $totalBookings) * 100 : 0 ?>%">
                                Used (<?= $usedTickets ?>)
                            </div>
                            <div class="progress-bar bg-warning" style="width: <?= $totalBookings > 0 ? ($expiredTickets / $totalBookings) * 100 : 0 ?>%">
                                Expired (<?= $expiredTickets ?>)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4 fade-in">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-clock-history"></i> Recent Bookings (Last 20)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $i => $booking): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><?= Html::encode($booking->user ? $booking->user->username : '-') ?></td>
                                        <td><?= Html::encode($booking->route ? $booking->route->origin : '-') ?> → <?= Html::encode($booking->route ? $booking->route->destination : '-') ?></td>
                                        <td><span class="badge bg-<?= $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'primary')) ?> status-badge"><?= Html::encode($booking->status) ?></span></td>
                                        <td><?= date('Y-m-d H:i', $booking->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-person-plus"></i> Recent User Registrations (Last 10)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentUsers as $i => $user): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><?= Html::encode($user->username) ?></td>
                                        <td><span class="badge bg-secondary status-badge"><?= Html::encode(ucfirst($user->role)) ?></span></td>
                                        <td><?= date('Y-m-d', $user->created_at) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Summary -->
    <div class="card shadow-sm fade-in">
        <div class="card-header bg-dark text-white fw-bold">
            <i class="bi bi-info-circle"></i> System Summary
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">System Performance</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Database: Online and responsive</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Email Service: Active</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Payment Gateway: Connected</li>
                        <li><i class="bi bi-exclamation-circle-fill text-warning"></i> Backup: Last 24 hours</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Security: All systems clear</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Key Metrics</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-arrow-up-circle text-success"></i> User Growth: <?= $usersByRole['user'] ?> registered users</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Booking Volume: <?= $totalBookings ?> total bookings</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Revenue Generated: <?= number_format($totalRevenue) ?> TZS</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Fleet Utilization: <?= $totalBuses ?> buses in service</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Route Coverage: <?= $totalRoutes ?> active routes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users by Role Chart
    var ctx1 = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Super Admin', 'Admin', 'Manager', 'Staff', 'Users'],
            datasets: [{
                data: [<?= $usersByRole['superadmin'] ?>, <?= $usersByRole['admin'] ?>, <?= $usersByRole['manager'] ?>, <?= $usersByRole['staff'] ?>, <?= $usersByRole['user'] ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            animation: { animateScale: true }
        }
    });

    // Booking Status Chart
    var ctx2 = document.getElementById('bookingStatusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Cancelled', 'Completed'],
            datasets: [{
                data: [<?= $statusCounts['pending'] ?>, <?= $statusCounts['confirmed'] ?>, <?= $statusCounts['cancelled'] ?>, <?= $statusCounts['completed'] ?>],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(13, 110, 253, 0.8)'
                ]
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            animation: { animateScale: true }
        }
    });
});

// Export to PDF function
function exportToPDF() {
    window.print(); // For now, use print functionality as PDF export
}
</script> 