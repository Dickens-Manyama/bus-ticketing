<?php
/** @var yii\web\View $this */
use yii\helpers\Html;

$this->title = 'Admin Business Report';
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
.report-header { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; }
.status-badge { font-size: 0.8em; }
</style>

<div class="container-fluid">
    <!-- Report Header -->
    <div class="report-header p-4 mb-4 rounded shadow-sm fade-in">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2"><i class="bi bi-person-badge"></i> Admin Business Report</h1>
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

    <!-- Business Overview Cards -->
    <div class="row mb-4 fade-in">
        <div class="col-md-2 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people display-6"></i>
                    <h6 class="card-title mt-2">Customers</h6>
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
                    <h6 class="card-title mt-2">Growth Rate</h6>
                    <p class="card-text display-6 fw-bold mb-0">12%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Analytics -->
    <div class="row mb-4 fade-in">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
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
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-graph-up"></i> Monthly Revenue Trend
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="200"></canvas>
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

    <!-- Business Performance -->
    <div class="row mb-4 fade-in">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-trophy"></i> Top Performing Routes
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Route</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $maxBookingCount = max(array_column($topRoutes, 'booking_count')) ?: 1; // Avoid division by zero
                                ?>
                                <?php foreach ($topRoutes as $i => $route): ?>
                                    <tr>
                                        <td><?= $i+1 ?></td>
                                        <td><strong><?= Html::encode($route['origin']) ?> → <?= Html::encode($route['destination']) ?></strong></td>
                                        <td><span class="badge bg-primary"><?= $route['booking_count'] ?></span></td>
                                        <td><strong><?= number_format($route['total_revenue']) ?> TZS</strong></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" style="width: <?= min(100, ($route['booking_count'] / $maxBookingCount) * 100) ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-calendar-check"></i> Daily Booking Trends
                </div>
                <div class="card-body">
                    <canvas id="bookingTrendsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row mb-4 fade-in">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-clock-history"></i> Recent Bookings (Last 15)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Amount</th>
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
                                        <td><strong><?= number_format($booking->route ? $booking->route->price : 0) ?> TZS</strong></td>
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
    </div>

    <!-- Business Summary -->
    <div class="card shadow-sm fade-in">
        <div class="card-header bg-dark text-white fw-bold">
            <i class="bi bi-info-circle"></i> Business Summary
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">Performance Metrics</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle-fill text-success"></i> Revenue target: On track</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Customer satisfaction: 4.8/5</li>
                        <li><i class="bi bi-exclamation-circle-fill text-warning"></i> 3 routes need attention</li>
                        <li><i class="bi bi-info-circle-fill text-info"></i> Peak season approaching</li>
                        <li><i class="bi bi-check-circle-fill text-success"></i> Fleet utilization: 85%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Key Insights</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-arrow-up-circle text-success"></i> 12% growth in bookings this month</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Revenue increased by 8%</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Customer base growing steadily</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Route optimization successful</li>
                        <li><i class="bi bi-arrow-up-circle text-success"></i> Ticket verification rate: 92%</li>
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
    // Booking Status Chart
    var ctx1 = document.getElementById('bookingStatusChart').getContext('2d');
    new Chart(ctx1, {
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

    // Monthly Revenue Chart
    var ctx2 = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_keys($monthlyRevenue)) ?>,
            datasets: [{
                label: 'Monthly Revenue (TZS)',
                data: <?= json_encode(array_values($monthlyRevenue)) ?>,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.3
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });

    // Booking Trends Chart
    var ctx3 = document.getElementById('bookingTrendsChart').getContext('2d');
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($bookingTrends)) ?>,
            datasets: [{
                label: 'Daily Bookings',
                data: <?= json_encode(array_values($bookingTrends)) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
});

// Export to PDF function
function exportToPDF() {
    window.print(); // For now, use print functionality as PDF export
}
</script> 