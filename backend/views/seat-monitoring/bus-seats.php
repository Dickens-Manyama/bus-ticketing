<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "Seat Map - Bus {$bus->plate_number}";
$this->params['breadcrumbs'][] = ['label' => 'Seat Monitoring', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
/* Ensure proper spacing from application header */
.bus-seats-view {
    margin-top: 20px;
}

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
</style>

<div class="bus-seats-view">
    <!-- Navigation Bar -->
    <div class="navigation-bar">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 text-muted">
                    <i class="bi bi-gear"></i> Seat Monitoring System
                </h5>
            </div>
            <div class="col-md-6">
                <div class="nav-buttons justify-content-end">
                    <a href="<?= Url::to(['index']) ?>" class="nav-btn nav-btn-back">
                        <i class="bi bi-arrow-left"></i> Back to Monitoring
                    </a>
                    <a href="<?= Url::to(['/dashboard/index']) ?>" class="nav-btn nav-btn-dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-0">
                                <i class="bi bi-bus-front"></i> Bus <?= Html::encode($bus->plate_number) ?> - Seat Map
                            </h3>
                            <small>Route: <?= $bus->route ? $bus->route->origin . ' → ' . $bus->route->destination : 'No Route' ?></small>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="badge bg-success fs-6">
                                <i class="bi bi-wifi"></i> Live Updates
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bus Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-primary">Total Seats</h5>
                                    <h2 class="mb-0"><?= $bus->seat_count ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Available</h5>
                                    <h2 class="mb-0" id="available-seats"><?= $bus->seat_count - count($bookedSeats) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-danger">Booked</h5>
                                    <h2 class="mb-0" id="booked-seats"><?= count($bookedSeats) ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">Occupancy</h5>
                                    <h2 class="mb-0" id="occupancy-rate"><?= $bus->seat_count > 0 ? round((count($bookedSeats) / $bus->seat_count) * 100, 1) : 0 ?>%</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seat Map -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> Seat Layout</h5>
                                </div>
                                <div class="card-body">
                                    <div class="bus-container">
                                        <div class="bus-layout-container">
                                            <!-- Seating Layout -->
                                            <?php
                                            $busType = $bus->type;
                                            $rows = 12;
                                            $seatIndex = 0;
                                            $seats = array_values($bus->seats); // Ensure numeric keys
                                            ?>
                                            <div class="seating-layout" id="seat-map">
                                                <?php for ($row = 0; $row < $rows; $row++): ?>
                                                    <div class="seat-row d-flex mb-2">
                                                <?php
                                                        if ($row === 0) {
                                                            // First row: left seats
                                                            $left = ($busType === 'Luxury') ? 1 : 2;
                                                            for ($i = 0; $i < $left; $i++) {
                                                                if (isset($seats[$seatIndex]) && $seats[$seatIndex]->seat_number !== 'D') {
                                                                    echo renderSeat($seats[$seatIndex]->seat_number, $seats[$seatIndex]);
                                                                    $seatIndex++;
                                                                }
                                                            }
                                                            // Aisle
                                                            echo '<div class="aisle-space"></div>';
                                                            // Right seats (all but last)
                                                            $right = ($busType === 'Middle Class') ? 3 : 2;
                                                            for ($i = 1; $i < $right; $i++) {
                                                                if (isset($seats[$seatIndex]) && $seats[$seatIndex]->seat_number !== 'D') {
                                                                    echo renderSeat($seats[$seatIndex]->seat_number, $seats[$seatIndex]);
                                                                    $seatIndex++;
                                                                }
                                                            }
                                                            // Driver seat
                                                            foreach ($seats as $s) {
                                                                if ($s->seat_number === 'D') {
                                                                    echo renderSeat('D', $s, true);
                                                                    break;
                                                                }
                                                            }
                                                        } else {
                                                            // Normal rows
                                                            $left = ($busType === 'Luxury') ? 1 : 2;
                                                            for ($i = 0; $i < $left; $i++) {
                                                                if (isset($seats[$seatIndex]) && $seats[$seatIndex]->seat_number !== 'D') {
                                                                    echo renderSeat($seats[$seatIndex]->seat_number, $seats[$seatIndex]);
                                                                    $seatIndex++;
                                                                        }
                                                                    }
                                                            // Aisle
                                                            echo '<div class="aisle-space"></div>';
                                                            $right = ($busType === 'Middle Class') ? 3 : 2;
                                                            for ($i = 0; $i < $right; $i++) {
                                                                if (isset($seats[$seatIndex]) && $seats[$seatIndex]->seat_number !== 'D') {
                                                                    echo renderSeat($seats[$seatIndex]->seat_number, $seats[$seatIndex]);
                                                                    $seatIndex++;
                                                            }
                                                        }
                                                    }
                                                        ?>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                            <?php
                                            function renderSeat($seatNum, $seat, $isDriver = false) {
                                                $class = 'seat';
                                                if ($isDriver || ($seat && $seat->status === 'driver')) {
                                                    $class .= ' driver-seat';
                                                    $icon = '<i class="bi bi-steering-wheel"></i>';
                                                    $title = 'Driver';
                                                } else if ($seat && $seat->status === 'booked') {
                                                    $class .= ' seat-booked';
                                                    $icon = '<i class="bi bi-person-x"></i>';
                                                    $title = 'Booked';
                                                } else if ($seat && $seat->status === 'pending') {
                                                    $class .= ' seat-pending';
                                                    $icon = '<i class="bi bi-person"></i>';
                                                    $title = 'Pending';
                                                } else if ($seat && $seat->status === 'toilet') {
                                                    $class .= ' toilet-seat';
                                                    $icon = '<i class="bi bi-droplet-half"></i>';
                                                    $title = 'Toilet';
                                                } else {
                                                    $class .= ' seat-available';
                                                    $icon = '<i class="bi bi-person"></i>';
                                                    $title = 'Available';
                                                }
                                                return "<div class='$class' data-seat='$seatNum' title='$title'>$icon<br><small>$seatNum</small></div>";
                                            }
                                            ?>

                                            <!-- Exit -->
                                            <div class="exit-area text-center mt-3">
                                                <div class="exit-sign">
                                                    <i class="bi bi-box-arrow-right"></i>
                                                    <div class="small text-muted">Exit</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Legend -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Legend</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box legend-available me-2"></div>
                                            <small>Available</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box legend-booked me-2"></div>
                                            <small>Booked (Confirmed)</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box legend-pending me-2"></div>
                                            <small>Pending</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box legend-driver me-2"></div>
                                            <small>Driver</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Bookings -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush" id="recent-bookings">
                                        <?php foreach (array_slice($bookings, 0, 10) as $booking): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="fw-bold">Seat <?= $booking->seat ? $booking->seat->seat_number : 'N/A' ?></small><br>
                                                    <small class="text-muted"><?= $booking->user ? $booking->user->username : 'Unknown' ?></small>
                                                </div>
                                                <span class="badge bg-<?= $booking->status === 'confirmed' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($booking->status) ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bus-container {
    display: flex;
    justify-content: center;
    padding: 20px;
}

.bus-layout-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
}

.exit-area {
    position: absolute;
    left: 20px;
    bottom: 20px;
}

.exit-sign {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.seating-layout {
    padding: 15px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.seat-row {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.row-number {
    background: #007bff;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.seats-container {
    display: flex;
    gap: 5px;
    flex: 1;
}

.seat {
    width: 40px;
    height: 40px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.seat-available {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border-color: #28a745;
}

.seat-booked {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border-color: #dc3545;
}

.seat-pending {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: white;
    border-color: #ffc107;
}

.seat-empty {
    background: #f8f9fa;
    color: #6c757d;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.driver-seat {
    background: linear-gradient(135deg, #343a40 0%, #212529 100%);
    color: white;
    border-color: #212529;
    cursor: not-allowed;
}

.aisle-space {
    width: 30px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 0.8rem;
}

.legend-box {
    width: 20px;
    height: 20px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
}

.legend-available {
    background: #28a745;
    border-color: #28a745;
}

.legend-booked {
    background: #dc3545;
    border-color: #dc3545;
}

.legend-pending {
    background: #ffc107;
    border-color: #ffc107;
}

.legend-driver {
    background: #343a40;
    border-color: #212529;
}
</style>

<script>
// Auto-refresh seat data every 10 seconds
setInterval(function() {
    updateSeatData();
}, 10000);

function updateSeatData() {
    $.get('<?= Url::to(['get-seat-data', 'bus_id' => $bus->id]) ?>')
        .done(function(data) {
            if (data.error) {
                console.error('Error fetching seat data:', data.error);
                return;
            }
            
            // Update statistics
            $('#available-seats').text(data.available_seats);
            $('#booked-seats').text(data.booked_seats.length);
            $('#occupancy-rate').text(data.occupancy_rate + '%');
            
            // Update seat map
            updateSeatMap(data.booked_seats, data.booking_details);
        })
        .fail(function() {
            console.error('Failed to fetch seat data');
        });
}

function updateSeatMap(bookedSeats, bookingDetails) {
    // Reset all seats to available
    $('.seat').removeClass('seat-booked seat-pending').addClass('seat-available');
    $('.seat').find('i').removeClass('bi-person-x').addClass('bi-person');
    
    // Update booked seats
    bookingDetails.forEach(function(booking) {
        const seatElement = $(`.seat[data-seat="${booking.seat_number}"]`);
        if (seatElement.length) {
            seatElement.removeClass('seat-available');
            if (booking.status === 'confirmed') {
                seatElement.addClass('seat-booked');
                seatElement.find('i').removeClass('bi-person').addClass('bi-person-x');
            } else {
                seatElement.addClass('seat-pending');
            }
            seatElement.attr('data-booking-id', booking.id);
            seatElement.attr('title', `Seat ${booking.seat_number} - ${booking.status} - ${booking.user}`);
        }
    });
}

// Initial update
updateSeatData();
</script> 