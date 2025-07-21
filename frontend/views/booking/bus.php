<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $buses common\models\Bus[] */

$this->title = 'Select Bus';
$this->params['breadcrumbs'][] = ['label' => 'Book Ticket', 'url' => ['bus']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="h2 mb-3">
                    <i class="bi bi-bus-front text-primary"></i> Select Your Bus
                </h1>
                <p class="text-muted">Choose from our range of buses with different comfort levels and seating arrangements</p>
            </div>
        </div>
    </div>

    <!-- Bus Class Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-funnel"></i> Filter by Bus Class
                    </h5>
                    <div class="btn-group" role="group" aria-label="Bus class filter">
                        <input type="radio" class="btn-check" name="busClass" id="all" value="" checked>
                        <label class="btn btn-outline-primary" for="all">All Classes</label>
                        
                        <input type="radio" class="btn-check" name="busClass" id="luxury" value="luxury">
                        <label class="btn btn-outline-success" for="luxury">
                            <i class="bi bi-star-fill"></i> Luxury
                        </label>
                        
                        <input type="radio" class="btn-check" name="busClass" id="semi_luxury" value="semi_luxury">
                        <label class="btn btn-outline-warning" for="semi_luxury">
                            <i class="bi bi-star"></i> Semi-Luxury
                        </label>
                        
                        <input type="radio" class="btn-check" name="busClass" id="middle_class" value="middle_class">
                        <label class="btn btn-outline-info" for="middle_class">
                            <i class="bi bi-star"></i> Middle Class
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bus Selection -->
    <div class="row" id="busContainer">
        <?php foreach ($buses as $bus): ?>
            <?php if (in_array($bus->class, ['express', 'economy'])) continue; ?>
            <div class="col-lg-6 col-xl-4 mb-4 bus-card" data-class="<?= $bus->class ?>">
                <div class="card h-100 shadow-sm hover-shadow">
                    <!-- Bus Class Badge -->
                    <div class="card-header bg-<?= $bus->getClassColor() ?> text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-bus-front"></i> <?= Html::encode($bus->type) ?>
                            </h5>
                            <span class="badge bg-light text-dark">
                                <?= $bus->getClassLabel() ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Bus Image Placeholder -->
                        <div class="text-center mb-3">
                            <div class="bg-light rounded p-4">
                                <i class="bi bi-bus-front display-4 text-muted"></i>
                            </div>
                        </div>
                        
                        <!-- Bus Details -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Plate Number</small>
                                    <p class="mb-1 fw-bold"><?= Html::encode($bus->plate_number) ?></p>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Seating</small>
                                    <p class="mb-1 fw-bold"><?= $bus->getSeatingConfigLabel() ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Capacity</small>
                                    <p class="mb-1 fw-bold"><?= $bus->seat_count ?> Seats</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <?php if ($bus->description): ?>
                            <div class="mb-3">
                                <small class="text-muted">Description</small>
                                <p class="mb-0 small"><?= Html::encode($bus->description) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Amenities -->
                        <?php
                        $amenities = $bus->getAmenitiesArray();
                        if ($bus->class === 'luxury' && $bus->seating_config === '2x2' && !in_array('Toilet', $amenities)) {
                            $amenities[] = 'Toilet';
                        }
                        ?>
                        <?php if ($amenities): ?>
                            <div class="mb-3">
                                <small class="text-muted">Amenities</small>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($amenities as $amenity): ?>
                                        <span class="badge bg-light text-dark small">
                                            <i class="bi bi-check-circle-fill text-success"></i> <?= Html::encode($amenity) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Seating Layout Preview -->
                        <div class="mb-3">
                            <small class="text-muted">Seating Layout</small>
                            <div class="seating-preview mt-2">
                                <?php
                                $layout = $bus->getSeatingLayout();
                                $rows = $layout['rows'];
                                $cols = $layout['cols'];
                                $aislePositions = [];
                                if (isset($layout['aisle_position'])) {
                                    $aislePositions = is_array($layout['aisle_position']) ? $layout['aisle_position'] : [$layout['aisle_position']];
                                }
                                ?>
                                <div class="bus-layout" style="font-size: 0.7rem;">
                                    <?php
                                    // Determine layout for preview
                                    $previewRows = 5;
                                    $left = 2; $right = 2; $aislePos = 2;
                                    if (strtolower($bus->class) === 'luxury') {
                                        $left = 1; $right = 2; $aislePos = 2; $previewRows = 5;
                                    } elseif (strtolower($bus->class) === 'semi_luxury') {
                                        $left = 2; $right = 2; $aislePos = 2; $previewRows = 5;
                                    } elseif (strtolower($bus->class) === 'middle_class') {
                                        $left = 2; $right = 3; $aislePos = 2; $previewRows = 4;
                                    }
                                    $totalCols = $left + $right;
                                    for ($row = 1; $row <= $previewRows; $row++): ?>
                                        <div class="row mb-1">
                                            <div class="col-12 d-flex justify-content-center align-items-center">
                                                <?php
                                                // Left side
                                                for ($col = 1; $col <= $left; $col++) {
                                                    echo '<div class="seat-available mx-1"><i class="bi bi-person"></i></div>';
                                                }
                                                // Aisle
                                                        echo '<div class="seat-aisle mx-1">|</div>';
                                                // Right side (no toilet or driver seat in preview)
                                                for ($col = 1; $col <= $right; $col++) {
                                                    echo '<div class="seat-available mx-1"><i class="bi bi-person"></i></div>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                    <?php if ($rows > $previewRows): ?>
                                        <div class="text-center text-muted small">
                                            <i class="bi bi-ellipsis"></i> <?= $rows - $previewRows ?> more rows
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-grid">
                            <a href="<?= Url::to(['route', 'bus_id' => $bus->id]) ?>" class="btn btn-primary">
                                <i class="bi bi-arrow-right"></i> Select This Bus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- No Buses Message -->
    <div class="row" id="noBusesMessage" style="display: none;">
        <div class="col-12">
            <div class="text-center py-5">
                <div class="display-4 text-muted mb-3">
                    <i class="bi bi-bus-x"></i>
                </div>
                <h3 class="text-muted mb-3">No Buses Found</h3>
                <p class="text-muted">No buses match your selected criteria. Please try a different filter.</p>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.card {
    transition: all 0.3s ease;
}

.seating-preview {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 0.5rem;
}

.seat-available {
    color: #28a745;
}

.seat-aisle {
    color: #6c757d;
    font-weight: bold;
}

.bus-layout {
    max-width: 200px;
    margin: 0 auto;
}

.btn-check:checked + .btn {
    border-width: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const busCards = document.querySelectorAll('.bus-card');
    const radioButtons = document.querySelectorAll('input[name="busClass"]');
    const noBusesMessage = document.getElementById('noBusesMessage');
    
    function filterBuses() {
        const selectedClass = document.querySelector('input[name="busClass"]:checked').value;
        let visibleCount = 0;
        
        busCards.forEach(card => {
            const busClass = card.dataset.class;
            if (!selectedClass || busClass === selectedClass) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide no buses message
        if (visibleCount === 0) {
            noBusesMessage.style.display = 'block';
        } else {
            noBusesMessage.style.display = 'none';
        }
    }
    
    // Add event listeners to radio buttons
    radioButtons.forEach(radio => {
        radio.addEventListener('change', filterBuses);
    });
});
</script> 