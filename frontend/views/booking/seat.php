<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $bus common\models\Bus */
/* @var $route common\models\Route */
/* @var $seats common\models\Seat[] */
/* @var $bookedSeats array */

$this->title = 'Select Seat';
$this->params['breadcrumbs'][] = ['label' => 'Book Ticket', 'url' => ['bus']];
$this->params['breadcrumbs'][] = ['label' => 'Select Bus', 'url' => ['route', 'bus_id' => $bus->id]];
$this->params['breadcrumbs'][] = ['label' => 'Select Route', 'url' => ['seat', 'bus_id' => $bus->id, 'route_id' => $route->id]];
$this->params['breadcrumbs'][] = $this->title;

// Amenities
$amenities = $bus->getAmenitiesArray();
if ($bus->class === 'luxury' && $bus->seating_config === '2x2' && !in_array('Toilet', $amenities)) {
    $amenities[] = 'Toilet';
}

function renderSeat($seatNum, $seat, $isDriver = false) {
    if ($seat && $seat->seat_number === 'Toilet' && $seat->status === 'toilet') {
        // Render toilet seat
        return "<div class='seat toilet-seat' style='width:60px;height:60px;border-radius:12px;background:linear-gradient(135deg,#17a2b8,#138496);color:white;display:flex;flex-direction:column;align-items:center;justify-content:center;border:3px solid #138496;box-shadow:0 2px 8px rgba(0,0,0,0.2);margin:0 10px;'>"
            . "<span style='font-size:2rem;'><i class='bi bi-droplet-half'></i></span>"
            . "<span style='font-size:0.9rem;font-weight:bold;'>Toilet</span>"
            . "</div>";
    }
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
    } else {
        $class .= ' seat-available seat-selectable';
        $icon = '<i class="bi bi-person"></i>';
        $title = 'Available';
    }
    $dataId = $seat ? 'data-seat-id="' . $seat->id . '"' : '';
    $dataNum = 'data-seat-number="' . $seatNum . '"';
    return "<div class='$class' $dataId $dataNum title='$title'>$icon<span class='seat-number'>$seatNum</span></div>";
}

// Build a map of seat_number => seat object for fast lookup
$seatMap = [];
foreach ($seats as $s) {
    $seatMap[$s->seat_number] = $s;
}

$layout = $bus->getSeatingLayout();
$rows = $layout['rows'];
$pattern = $layout['pattern'];
$cols = $layout['cols'];
$aislePositions = $layout['aisle_positions'];
$seatsPerRow = $layout['seats_per_row'];

// Generate seat labels for each row/col
function getSeatLabel($row, $col, $pattern) {
    $rowLetter = chr(65 + $row); // A, B, C, ...
    $seatNum = 1;
    $colIndex = 0;
    foreach ($pattern as $p) {
        if ($p === 'aisle') {
            $colIndex++;
            if ($colIndex - 1 == $col) return null;
        } else {
            for ($i = 0; $i < $p; $i++) {
                if ($colIndex == $col) {
                    return $rowLetter . ($i + 1);
                }
                $colIndex++;
            }
        }
    }
    return null;
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="h2 mb-3">
                    <i class="bi bi-person-seat text-primary"></i> Select Your Seat
                </h1>
                <p class="text-muted">Choose your preferred seat on the <?= Html::encode($bus->type) ?></p>
            </div>
        </div>
    </div>

    <!-- Bus and Route Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-bus-front"></i> Bus Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Bus Type</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($bus->type) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Plate Number</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($bus->plate_number) ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Class</small>
                            <p class="mb-1">
                                <span class="badge bg-<?= $bus->getClassColor() ?>"><?= $bus->getClassLabel() ?></span>
                            </p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Seating</small>
                            <p class="mb-1 fw-bold"><?= $bus->getSeatingConfigLabel() ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Route Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">From</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($route->origin) ?></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">To</small>
                            <p class="mb-1 fw-bold"><?= Html::encode($route->destination) ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Price</small>
                            <p class="mb-1 fw-bold text-success"><?= number_format($route->price) ?> TZS</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Duration</small>
                            <!-- Removed duration display: property does not exist -->
                            <p class="mb-1 fw-bold">N/A</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seat Selection -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-grid-3x3-gap"></i> Seat Layout - <?= $bus->getSeatingConfigLabel() ?>
                        <span class="badge bg-light text-dark ms-2"><?= count($seats) ?> Total Seats</span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Seat Legend -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <div class="legend-box legend-available me-2"></div>
                                    <small>Available</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-box legend-booked me-2"></div>
                                    <small>Booked</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-box legend-selected me-2"></div>
                                    <small>Selected</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-box legend-driver me-2"></div>
                                    <small>Driver</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="legend-box legend-toilet me-2" style="background:linear-gradient(135deg,#17a2b8,#138496);border-color:#138496;"><i class="bi bi-droplet-half"></i></div>
                                    <small>Toilet</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Seat Info -->
                    <div class="row mb-4" id="selected-seat-info" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-success text-center">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>Seat <span id="selected-seat-number"></span></strong> has been selected!
                                <br>
                                <small class="text-muted">Click "Proceed to Review" to continue with your booking.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Bus Layout -->
                    <div class="bus-container">
                        <div class="bus-layout-container">
                            <!-- Seating Layout -->
                            <div class="seating-layout">
                                <?php
                                // Render driver seat if present
                                if (isset($seatMap['D'])) {
                                    echo "<div class='seat-row mb-2' style='justify-content: flex-end;'>";
                                    echo renderSeat('D', $seatMap['D'], true);
                                    echo "</div>";
                                }
                                // Render seat rows
                                for ($row = 0; $row < $rows; $row++): ?>
                                    <div class="seat-row mb-2">
                                        <?php
                                        $colIndex = 0;
                                        foreach ($pattern as $p) {
                                            if ($p === 'aisle') {
                                                echo '<div class="aisle-space"></div>';
                                                $colIndex++;
                                            } else {
                                                for ($i = 0; $i < $p; $i++) {
                                                    $seatLabel = chr(65 + $row) . ($i + 1);
                                                    $seat = $seatMap[$seatLabel] ?? null;
                                                    echo renderSeat($seatLabel, $seat);
                                                    $colIndex++;
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <!-- Exit -->
                            <div class="exit-area text-center mt-3">
                                <div class="exit-sign">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <div class="small text-muted">Exit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Proceed Button -->
                    <div class="row mt-4" id="proceed-section" style="display: none;">
                        <div class="col-12 text-center">
                            <a href="#" id="proceed-button" class="btn btn-success btn-lg">
                                <i class="bi bi-arrow-right-circle"></i> Proceed to Review
                            </a>
                        </div>
                    </div>
                    
                    <!-- Seat Information -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Seat Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Available Seats</small>
                                            <p class="mb-1 fw-bold text-success"><?= count($seats) - count($bookedSeats) ?></p>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Booked Seats</small>
                                            <p class="mb-1 fw-bold text-danger"><?= count($bookedSeats) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Tips</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0 small">
                                        <li><i class="bi bi-check-circle text-success me-1"></i> Click on any green seat to select it</li>
                                        <li><i class="bi bi-check-circle text-success me-1"></i> Selected seat will turn yellow</li>
                                        <li><i class="bi bi-check-circle text-success me-1"></i> Red seats are already booked</li>
                                        <li><i class="bi bi-check-circle text-success me-1"></i> Black seat is driver seat (not bookable)</li>
                                        <li><i class="bi bi-check-circle text-success me-1"></i> Click "Proceed to Review" after selection</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="<?= Url::to(['route', 'bus_id' => $bus->id]) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Routes
            </a>
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

.exit-sign .bi-box-arrow-right {
    font-size: 1.5rem;
    margin-bottom: 5px;
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
    align-items: center;
    gap: 8px;
    flex: 1;
}

.seat {
    width: 55px;
    height: 45px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.7rem;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.seat-available {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: 2px solid #28a745;
}

.seat-available:hover {
    background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.seat-selected {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    color: white !important;
    border: 2px solid #ffc107 !important;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4) !important;
}

.seat-booked {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: 2px solid #dc3545;
    cursor: not-allowed;
    opacity: 0.8;
}

.seat-empty {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    color: #6c757d;
    cursor: not-allowed;
}

.aisle-space {
    width: 40px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.2rem;
    background: rgba(108, 117, 125, 0.1);
    border-radius: 5px;
    border: 1px dashed #dee2e6;
}

.seat-number {
    font-size: 0.6rem;
    margin-top: 2px;
}

.toilet-block {
    background: linear-gradient(135deg, #b2f0ff 0%, #e0e0e0 100%);
    border: 2px solid #007bff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
    color: #007bff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}

/* Bus layout enhancements */
.bus-container {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.bus-layout-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    pointer-events: none;
    border-radius: 15px;
}

/* Legend styles */
.seat-available, .seat-booked, .seat-selected, .seat-aisle {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
}

.seat-aisle {
    background: #6c757d;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

/* Ensure horizontal layout for all screen sizes */
@media (min-width: 769px) {
    .seat {
        flex-direction: row !important;
        display: flex !important;
    }
    
    .seat-number {
        display: inline-block !important;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .seat {
        width: 50px;
        height: 40px;
        font-size: 0.7rem;
        flex-direction: row !important;
        display: flex !important;
    }
    
    .aisle-space {
        width: 20px;
        height: 40px;
    }
    
    .bus-layout-container {
        padding: 10px;
    }
    
    .row-number {
        width: 25px;
        height: 25px;
        font-size: 0.8rem;
    }
    
    .seat-number {
        font-size: 0.6rem;
        display: inline-block !important;
    }
}

/* Animation for seat selection */
@keyframes seatPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.seat-available:active {
    animation: seatPulse 0.2s ease-in-out;
}

.legend-box {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 2px solid #222;
    display: inline-block;
    margin-right: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.10);
}
.legend-available {
    background: #28a745;
    border-color: #218838;
}
.legend-booked {
    background: #dc3545;
    border-color: #b21f2d;
}
.legend-selected {
    background: #ffc107;
    border-color: #e0a800;
}
.legend-aisle {
    background: #adb5bd;
    border-color: #6c757d;
}

.driver-seat {
    background: linear-gradient(135deg, #343a40, #212529) !important;
    color: white !important;
    border: 2px solid #212529 !important;
    pointer-events: none !important;
    cursor: not-allowed !important;
}

.driver-seat .bi-steering-wheel {
    font-size: 1.3rem;
    margin-bottom: 2px;
}

.legend-driver {
    background: #343a40;
    border-color: #212529;
}
.legend-toilet {
    width: 20px;
    height: 20px;
    border: 2px solid #138496;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg,#17a2b8,#138496);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedSeatId = null;
    let selectedSeatNumber = null;
    
    // Handle seat selection
    const selectableSeats = document.querySelectorAll('.seat-selectable');
    
    selectableSeats.forEach(seat => {
        seat.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.seat-selected').forEach(prevSeat => {
                prevSeat.classList.remove('seat-selected');
                prevSeat.classList.add('seat-available');
            });
            
            // Select this seat
            this.classList.remove('seat-available');
            this.classList.add('seat-selected');
            
            // Store selected seat info
            selectedSeatId = this.getAttribute('data-seat-id');
            selectedSeatNumber = this.getAttribute('data-seat-number');
            
            // Show selected seat info
            document.getElementById('selected-seat-number').textContent = selectedSeatNumber;
            document.getElementById('selected-seat-info').style.display = 'block';
            
            // Show proceed button
            document.getElementById('proceed-section').style.display = 'block';
            
            // Scroll to proceed button
            setTimeout(() => {
                document.getElementById('proceed-section').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 300);
        });
        
        // Add hover effects
        seat.addEventListener('mouseenter', function() {
            if (!this.classList.contains('seat-selected')) {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 4px 12px rgba(40, 167, 69, 0.4)';
            }
        });
        
        seat.addEventListener('mouseleave', function() {
            if (!this.classList.contains('seat-selected')) {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = 'none';
            }
        });
    });
    
    // Handle proceed button click
    document.getElementById('proceed-button').addEventListener('click', function(e) {
        e.preventDefault();
        
        if (selectedSeatId) {
            // Navigate to payment page with selected seat
            const url = '<?= Url::to(['payment']) ?>?bus_id=<?= $bus->id ?>&route_id=<?= $route->id ?>&seat_id=' + selectedSeatId;
            window.location.href = url;
        } else {
            alert('Please select a seat first!');
        }
    });
    
    // Show seat number on hover for better visibility
    const allSeats = document.querySelectorAll('.seat');
    allSeats.forEach(seat => {
        const seatNumber = seat.getAttribute('data-seat-number');
        if (seatNumber) {
            seat.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            
            seat.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        }
    });
});
</script> 