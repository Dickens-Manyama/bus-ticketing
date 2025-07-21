<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $bookings common\models\Booking[] */

$this->title = 'My Bookings';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">
                    <i class="bi bi-list-ul text-primary"></i> My Bookings
                </h2>
                <div>
                    <?= Html::a('<i class="bi bi-bell"></i> Notifications', ['notifications'], ['class' => 'btn btn-outline-warning me-2']) ?>
                    <?= Html::a('<i class="bi bi-graph-up"></i> Statistics', ['statistics'], ['class' => 'btn btn-outline-info me-2']) ?>
                    <?= Html::a('<i class="bi bi-download"></i> Export CSV', ['export'], ['class' => 'btn btn-outline-success me-2']) ?>
                    <?= Html::a('<i class="bi bi-plus-circle"></i> Book New Ticket', ['bus'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= Html::encode($search ?? '') ?>" 
                                   placeholder="Bus, route, seat...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="confirmed" <?= ($status ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="cancelled" <?= ($status ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                <option value="pending" <?= ($status ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?= Html::encode($dateFrom ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="<?= Html::encode($dateTo ?? '') ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <a href="<?= Url::to(['my-bookings']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="text-center py-5">
                    <div class="display-4 text-muted mb-3">
                        <i class="bi bi-ticket-x"></i>
                    </div>
                    <h3 class="text-muted mb-3">No Bookings Found</h3>
                    <p class="text-muted mb-4">You haven't made any bookings yet. Start by booking your first ticket!</p>
                    <a href="<?= Url::to(['bus']) ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-ticket-detailed"></i> Book Your First Ticket
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm hover-shadow">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-ticket-perforated"></i> Booking #<?= $booking->id ?>
                                        </h6>
                                        <span class="badge bg-light text-dark">
                                            <?= ucfirst($booking->status) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="text-primary mb-2">
                                            <i class="bi bi-bus-front"></i> <?= Html::encode($booking->bus->type) ?>
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Plate:</strong> <?= Html::encode($booking->bus->plate_number) ?>
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-success mb-2">
                                            <i class="bi bi-geo-alt"></i> Route
                                        </h6>
                                        <p class="mb-1">
                                            <?= Html::encode($booking->route->origin) ?> 
                                            <i class="bi bi-arrow-right"></i> 
                                            <?= Html::encode($booking->route->destination) ?>
                                        </p>
                                        <p class="mb-0 text-success fw-bold">
                                            <?= number_format($booking->route->price) ?> TZS
                                        </p>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="text-info mb-2">
                                            <i class="bi bi-person-seat"></i> Seat Details
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Seat:</strong> <?= Html::encode($booking->seat->seat_number) ?>
                                        </p>
                                        <p class="mb-0">
                                            <strong>Date:</strong> <?= date('Y-m-d H:i', $booking->created_at) ?>
                                        </p>
                                    </div>
                                    
                                    <?php if ($booking->payment_method): ?>
                                        <div class="mb-3">
                                            <h6 class="text-warning mb-2">
                                                <i class="bi bi-credit-card"></i> Payment
                                            </h6>
                                            <p class="mb-1">
                                                <strong>Method:</strong> <?= Html::encode($booking->payment_method) ?>
                                            </p>
                                            <p class="mb-0">
                                                <strong>Status:</strong> 
                                                <span class="badge bg-success">Completed</span>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="d-grid gap-2">
                                        <?php if ($booking->status === 'confirmed'): ?>
                                            <a href="<?= Url::to(['receipt', 'id' => $booking->id]) ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> View Receipt
                                            </a>
                                            <a href="<?= Url::to(['pdf-receipt', 'id' => $booking->id]) ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                                                <i class="bi bi-file-pdf"></i> Download PDF
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmCancel(<?= $booking->id ?>)">
                                                <i class="bi bi-x-circle"></i> Cancel Booking
                                            </button>
                                            <div class="mt-2 text-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode('http://192.168.100.76:8080/booking/mobile-verify?id=' . $booking->id) ?>" alt="QR Code" title="Scan to verify ticket" />
                                                <div class="small text-muted">Scan to verify ticket</div>
                                            </div>
                                        <?php elseif ($booking->status === 'cancelled'): ?>
                                            <span class="text-muted small">
                                                <i class="bi bi-info-circle"></i> This booking has been cancelled
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">
                                                <i class="bi bi-clock"></i> Processing...
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Booking Statistics -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="bi bi-graph-up"></i> Booking Summary
                                </h5>
                                <div class="row text-center">
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-primary text-white p-3 rounded">
                                            <h4 class="mb-0"><?= count($bookings) ?></h4>
                                            <small>Total Bookings</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-success text-white p-3 rounded">
                                            <h4 class="mb-0"><?= count(array_filter($bookings, function($b) { return $b->status === 'confirmed'; })) ?></h4>
                                            <small>Confirmed</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-danger text-white p-3 rounded">
                                            <h4 class="mb-0"><?= count(array_filter($bookings, function($b) { return $b->status === 'cancelled'; })) ?></h4>
                                            <small>Cancelled</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="bg-info text-white p-3 rounded">
                                            <h4 class="mb-0"><?= number_format(array_sum(array_map(function($b) { return $b->route->price; }, array_filter($bookings, function($b) { return $b->status === 'confirmed'; })))) ?></h4>
                                            <small>TZS Spent</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function confirmCancel(bookingId) {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        window.location.href = '<?= Url::to(['cancel-booking']) ?>?id=' + bookingId;
    }
}
</script>

<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
    transition: all 0.3s ease;
}

.badge {
    font-size: 0.75rem;
}
</style> 