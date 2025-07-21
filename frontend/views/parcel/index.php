<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Parcel;

$this->title = 'Parcel Delivery Services';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="parcel-index">
    <!-- Hero Section -->
    <div class="hero-section text-center py-5 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px;">
        <div class="container">
            <h1 class="display-4 mb-3">
                <i class="bi bi-box-seam"></i> Parcel Delivery Services
            </h1>
            <p class="lead mb-4">Fast, reliable, and secure parcel delivery across Tanzania</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="feature-card">
                                <i class="bi bi-shield-check display-4"></i>
                                <h5>Secure Delivery</h5>
                                <p>Your parcels are safe with us</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="feature-card">
                                <i class="bi bi-lightning display-4"></i>
                                <h5>Fast Service</h5>
                                <p>Quick delivery across routes</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="feature-card">
                                <i class="bi bi-qr-code display-4"></i>
                                <h5>Track & Trace</h5>
                                <p>Real-time tracking with QR codes</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcel Types Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-list-ul"></i> Available Parcel Types
            </h2>
        </div>
        
        <?php
        $parcelTypes = Parcel::getParcelTypeLabels();
        $prices = Parcel::getParcelTypePrices();
        $colors = [
            'documents' => 'primary',
            'electronics' => 'info',
            'clothing' => 'success',
            'food' => 'warning',
            'fragile' => 'danger',
            'heavy' => 'dark',
            'express' => 'purple',
            'standard' => 'secondary',
        ];
        
        foreach ($parcelTypes as $type => $label):
            $price = $prices[$type];
            $color = $colors[$type] ?? 'secondary';
        ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-box-seam display-4 text-<?= $color ?>"></i>
                    </div>
                    <h5 class="card-title"><?= ucfirst($type) ?></h5>
                    <p class="card-text text-muted"><?= $label ?></p>
                    <div class="price-badge">
                        <span class="badge bg-<?= $color ?> fs-6"><?= number_format($price, 0, '.', ',') ?> TZS</span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-lightning"></i> Quick Actions
            </h2>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-plus-circle display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Book New Parcel</h5>
                    <p class="card-text">Send your parcel to any destination on our routes</p>
                    <?= Html::a('Book Now', ['create'], ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-search display-4 text-info mb-3"></i>
                    <h5 class="card-title">Track Parcel</h5>
                    <p class="card-text">Track your parcel using tracking number</p>
                    <?= Html::a('Track Now', ['track'], ['class' => 'btn btn-info']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-clock-history display-4 text-success mb-3"></i>
                    <h5 class="card-title">My Parcels</h5>
                    <p class="card-text">View your parcel history and status</p>
                    <?= Html::a('View History', ['my-parcels'], ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Routes -->
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-geo-alt"></i> Available Routes
            </h2>
        </div>
        
        <?php foreach ($routes as $route): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bus-front"></i> <?= Html::encode($route->name) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <strong>From:</strong><br>
                            <span class="text-primary"><?= Html::encode($route->origin) ?></span>
                        </div>
                        <div class="col-6">
                            <strong>To:</strong><br>
                            <span class="text-success"><?= Html::encode($route->destination) ?></span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Distance:</strong><br>
                            <span class="text-muted"><?= Html::encode($route->distance) ?> km</span>
                        </div>
                        <div class="col-6">
                            <strong>Duration:</strong><br>
                            <span class="text-muted"><?= Html::encode($route->duration) ?> hrs</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <?= Html::a('Book Parcel', ['create', 'route_id' => $route->id], ['class' => 'btn btn-primary btn-sm w-100']) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- How It Works -->
    <div class="row mt-5">
        <div class="col-12">
            <h2 class="text-center mb-4">
                <i class="bi bi-question-circle"></i> How It Works
            </h2>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="step-card">
                <div class="step-number">1</div>
                <i class="bi bi-pencil-square display-4 text-primary mb-3"></i>
                <h5>Book Online</h5>
                <p>Fill out the parcel booking form with sender and recipient details</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="step-card">
                <div class="step-number">2</div>
                <i class="bi bi-credit-card display-4 text-success mb-3"></i>
                <h5>Pay Online</h5>
                <p>Pay securely using mobile money, bank transfer, or cash</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="step-card">
                <div class="step-number">3</div>
                <i class="bi bi-qr-code display-4 text-info mb-3"></i>
                <h5>Get QR Code</h5>
                <p>Receive a unique QR code for tracking your parcel</p>
            </div>
        </div>
        
        <div class="col-md-3 text-center mb-4">
            <div class="step-card">
                <div class="step-number">4</div>
                <i class="bi bi-truck display-4 text-warning mb-3"></i>
                <h5>Track & Deliver</h5>
                <p>Track your parcel in real-time until delivery</p>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <?= Html::a('<i class="bi bi-plus-circle me-2"></i>Book Parcel Now', ['create'], ['class' => 'btn btn-primary btn-lg']) ?>
        <?= Html::a('<i class="bi bi-clock-history me-2"></i>My Parcels', ['my-parcels'], ['class' => 'btn btn-outline-primary btn-lg ms-2']) ?>
    </div>
</div>

<style>
.feature-card {
    padding: 20px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
}

.step-card {
    padding: 20px;
    position: relative;
}

.step-number {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.price-badge {
    margin-top: 15px;
}

.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
</style> 