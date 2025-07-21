<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Live Seat Monitoring Dashboard';
$this->params['breadcrumbs'][] = ['label' => 'Seat Monitoring', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
/* Navigation bar styles */
.navigation-bar {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.nav-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.nav-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.nav-btn-back {
    background: #6c757d;
    color: white;
}

.nav-btn-back:hover {
    background: #5a6268;
    color: white;
}

.nav-btn-dashboard {
    background: #007bff;
    color: white;
}

.nav-btn-dashboard:hover {
    background: #0056b3;
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .nav-buttons {
        flex-direction: column;
        gap: 8px;
    }
    
    .nav-btn {
        width: 100%;
        justify-content: center;
    }
}

/* Existing styles */
.bus-card {
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
}

.bus-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    transition: width 0.6s ease;
}

.bus-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.btn-group .btn {
    border-radius: 4px;
}

.btn-group .btn:not(:last-child) {
    margin-right: 2px;
}
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-speedometer2 text-primary"></i> Real-time Dashboard
                    </h1>
                    <p class="text-muted mb-0">Live bus monitoring and journey management</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul"></i> List View
                    </a>
                    <button class="btn btn-outline-secondary" onclick="location.reload()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-0">
                                <i class="bi bi-speedometer2"></i> Live Seat Monitoring Dashboard
                            </h3>
                            <small>Real-time updates every 10 seconds</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-light text-dark fs-6">
                                <i class="bi bi-wifi"></i> Live Updates Active
                            </div>
                            <button class="btn btn-light btn-sm ms-2" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Total Buses</h5>
                                    <h2 class="mb-0" id="total-buses"><?= count($busData) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Active Routes</h5>
                                    <h2 class="mb-0" id="active-routes">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">High Occupancy</h5>
                                    <h2 class="mb-0" id="high-occupancy">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-info">Available</h5>
                                    <h2 class="mb-0" id="total-available">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bus Grid -->
                    <div class="row" id="bus-grid">
                        <?php foreach ($busData as $bus): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card bus-card h-100" data-bus-id="<?= $bus['id'] ?>">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-bus-front"></i> <?= Html::encode($bus['plate_number']) ?>
                                        </h6>
                                        <span class="badge bg-<?= $bus['class'] === 'Luxury' ? 'warning' : 'info' ?>">
                                            <?= Html::encode($bus['class']) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <!-- Route Info -->
                                        <div class="mb-3">
                                            <small class="text-muted">Route:</small>
                                            <div class="fw-bold"><?= Html::encode($bus['route_info']) ?></div>
                                        </div>

                                        <!-- Occupancy Progress -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>Occupancy</small>
                                                <small class="occupancy-rate"><?= $bus['occupancy_rate'] ?>%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-<?= $bus['occupancy_rate'] >= 90 ? 'danger' : ($bus['occupancy_rate'] >= 70 ? 'warning' : 'success') ?>" 
                                                     style="width: <?= $bus['occupancy_rate'] ?>%"></div>
                                            </div>
                                        </div>

                                        <!-- Seat Statistics -->
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="border rounded p-2">
                                                    <small class="text-muted d-block">Total</small>
                                                    <strong class="total-seats"><?= $bus['total_seats'] ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border rounded p-2">
                                                    <small class="text-muted d-block">Booked</small>
                                                    <strong class="booked-seats text-danger"><?= $bus['booked_seats'] ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border rounded p-2">
                                                    <small class="text-muted d-block">Available</small>
                                                    <strong class="available-seats text-success"><?= $bus['available_seats'] ?></strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status and Actions -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?= $bus['route_status'] === 'in_progress' ? 'warning' : ($bus['route_status'] === 'completed' ? 'danger' : 'success') ?> route-status">
                                                <?= $bus['route_status'] === 'pending' ? 'Active' : ($bus['route_status'] === 'in_progress' ? 'On Journey' : ($bus['route_status'] === 'completed' ? 'Final Destination' : ucfirst(str_replace('_', ' ', $bus['route_status'])))) ?>
                                            </span>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= Url::to(['bus-seats', 'bus_id' => $bus['id']]) ?>" 
                                                   class="btn btn-outline-primary" title="View Seat Map">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button class="btn btn-outline-success start-journey-btn" 
                                                        onclick="startJourney(<?= $bus['id'] ?>)" 
                                                        title="Start Journey">
                                                    <i class="bi bi-play-circle"></i> Start Journey
                                                </button>
                                                <button class="btn btn-outline-warning finish-journey-btn" 
                                                        onclick="finishJourney(<?= $bus['id'] ?>)" 
                                                        title="Finish Journey">
                                                    <i class="bi bi-stop-circle"></i> Finish Journey
                                                </button>
                                                <button class="btn btn-outline-info start-new-journey-btn" 
                                                        onclick="startNewJourney(<?= $bus['id'] ?>)" 
                                                        title="Start New Journey">
                                                    <i class="bi bi-arrow-clockwise"></i> Start New Journey
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- No Buses Message -->
                    <?php if (empty($busData)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bus-front display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No Buses Available</h4>
                            <p class="text-muted">There are no buses configured in the system.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let updateInterval;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    updateDashboard();
    updateInterval = setInterval(updateDashboard, 10000); // Update every 10 seconds
});

function updateDashboard() {
    // Update summary statistics
    updateSummaryStats();
    
    // Update individual bus cards
    updateBusCards();
}

function updateSummaryStats() {
    const buses = document.querySelectorAll('.bus-card');
    let activeRoutes = 0;
    let highOccupancy = 0;
    let totalAvailable = 0;
    
    buses.forEach(bus => {
        const routeStatus = bus.querySelector('.route-status').textContent.trim();
        const occupancyRate = parseInt(bus.querySelector('.occupancy-rate').textContent);
        const availableSeats = parseInt(bus.querySelector('.available-seats').textContent);
        
        if (routeStatus === 'In Progress') activeRoutes++;
        if (occupancyRate >= 90) highOccupancy++;
        totalAvailable += availableSeats;
    });
    
    document.getElementById('active-routes').textContent = activeRoutes;
    document.getElementById('high-occupancy').textContent = highOccupancy;
    document.getElementById('total-available').textContent = totalAvailable;
}

function updateBusCards() {
    const buses = document.querySelectorAll('.bus-card');
    
    buses.forEach(bus => {
        const busId = bus.dataset.busId;
        
        // Fetch updated data for this bus
        $.get('<?= Url::to(['get-seat-data']) ?>', {bus_id: busId})
            .done(function(data) {
                if (data.error) return;
                
                // Update occupancy rate
                const occupancyElement = bus.querySelector('.occupancy-rate');
                occupancyElement.textContent = data.occupancy_rate + '%';
                
                // Update progress bar
                const progressBar = bus.querySelector('.progress-bar');
                progressBar.style.width = data.occupancy_rate + '%';
                progressBar.className = 'progress-bar bg-' + (data.occupancy_rate >= 90 ? 'danger' : (data.occupancy_rate >= 70 ? 'warning' : 'success'));
                
                // Update seat counts
                bus.querySelector('.booked-seats').textContent = data.booked_seats.length;
                bus.querySelector('.available-seats').textContent = data.available_seats;
                
                // Update route status
                const statusElement = bus.querySelector('.route-status');
                let statusText = '';
                let statusClass = '';
                
                switch (data.route_status) {
                    case 'pending':
                        statusText = 'Active';
                        statusClass = 'success';
                        break;
                    case 'in_progress':
                        statusText = 'On Journey';
                        statusClass = 'warning';
                        break;
                    case 'completed':
                        statusText = 'Final Destination';
                        statusClass = 'danger';
                        break;
                    default:
                        statusText = data.route_status.charAt(0).toUpperCase() + data.route_status.slice(1);
                        statusClass = 'secondary';
                }
                
                statusElement.textContent = statusText;
                statusElement.className = 'badge bg-' + statusClass;
                
                // Update action buttons
                const startBtn = bus.querySelector('.start-route-btn');
                const finishBtn = bus.querySelector('.finish-route-btn');
                
                startBtn.disabled = data.route_status === 'in_progress';
                finishBtn.disabled = data.route_status === 'completed';
            })
            .fail(function() {
                console.error('Failed to update bus data for bus ID:', busId);
            });
    });
}

function startJourney(busId) {
    alert('startJourney called for busId: ' + busId);
    console.log('startJourney called with busId:', busId);
    if (confirm('Are you sure you want to start the journey for this bus?')) {
        $.ajax({
            url: '<?= Url::to(['start-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('startJourney success response:', response);
                if (response.success) {
                    alert('Journey started! Bus is now on the road.');
                    updateDashboard();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('startJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to start journey. Please try again.');
            }
        });
    }
}

function finishJourney(busId) {
    alert('finishJourney called for busId: ' + busId);
    console.log('finishJourney called with busId:', busId);
    if (confirm('Are you sure you want to finish the current journey for this bus?')) {
        $.ajax({
            url: '<?= Url::to(['finish-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('finishJourney success response:', response);
                if (response.success) {
                    alert('Journey finished! All seats are now available.');
                    updateDashboard();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('finishJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to finish journey. Please try again.');
            }
        });
    }
}

function startNewJourney(busId) {
    alert('startNewJourney called for busId: ' + busId);
    console.log('startNewJourney called with busId:', busId);
    if (confirm('Are you sure you want to start a new journey for this bus? This will prevent new bookings.')) {
        $.ajax({
            url: '<?= Url::to(['start-new-journey']) ?>',
            type: 'POST',
            data: {
                bus_id: busId,
                <?= Yii::$app->request->csrfParam ?>: '<?= Yii::$app->request->csrfToken ?>'
            },
            dataType: 'json',
            success: function(response) {
                console.log('startNewJourney success response:', response);
                if (response.success) {
                    alert('New journey started! Bus is ready for bookings.');
                    updateDashboard();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('startNewJourney AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to start new journey. Please try again.');
            }
        });
    }
}

// Clean up interval when page is unloaded
window.addEventListener('beforeunload', function() {
    if (updateInterval) {
        clearInterval(updateInterval);
    }
});
</script> 