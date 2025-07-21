<?php
use yii\helpers\Html;
use common\models\Bus;
use common\models\Route;
use common\models\Booking;
use common\models\User;

$this->title = 'Bus Ticketing System';

// Fetch stats
$totalBookings = Booking::find()->count();
$totalBuses = Bus::find()->count();
$topRoutes = Route::find()->limit(4)->all();
$busTypes = Bus::find()
    ->select(['type', 'image' => new \yii\db\Expression('MIN(image)')])
    ->groupBy('type')
    ->asArray()
    ->all();

$userBookings = null;
if (!Yii::$app->user->isGuest) {
    $user = Yii::$app->user->identity;
    if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
        $userBookings = null; // Admins see total
    } else {
        $userBookings = \common\models\Booking::find()->where(['user_id' => $user->id])->count();
    }
}

// Register Bootstrap Icons CDN
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');

// Register custom JS for animated counters
$this->registerJs(<<<JS
function animateCounter(id, end) {
  let el = document.getElementById(id);
  let start = 0;
  let duration = 1000;
  let step = Math.ceil(end / (duration / 16));
  let interval = setInterval(function() {
    start += step;
    if (start >= end) {
      el.textContent = end;
      clearInterval(interval);
    } else {
      el.textContent = start;
    }
  }, 16);
}
animateCounter('bookings-counter', $totalBookings);
animateCounter('buses-counter', $totalBuses);
JS
);
?>
<div class="container py-5">
    <!-- Hero Section -->
    <div class="welcome-banner mb-4 p-4 rounded-4 shadow-lg text-center text-white" style="background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);">
        <h1 class="display-3 fw-bold mb-2" style="letter-spacing:1px;">Welcome to Dickens-OnlineTicketing</h1>
        <p class="lead mb-0">Your one-stop platform for booking bus tickets online across Tanzania. Fast, secure, and convenient!</p>
    </div>
    
    <!-- Hero Section with buttons -->
    <div class="row align-items-center mb-5 bg-gradient p-4 rounded-4 shadow-lg" style="background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%); color: #fff;">
        <div class="col-md-6 text-center text-md-start">
            <h2 class="display-5 fw-bold mb-3">Book your bus tickets online, choose your seat, and travel with ease!</h2>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?= Html::a('<i class="bi bi-ticket-detailed"></i> Book a Ticket', ['/booking/bus'], ['class' => 'btn btn-light btn-lg fw-bold shadow']) ?>
                
                <?php if (Yii::$app->user->isGuest): ?>
                <?= Html::a('<i class="bi bi-person-plus"></i> Sign Up', ['/site/signup'], ['class' => 'btn btn-light btn-lg fw-bold shadow']) ?>
                <?= Html::a('<i class="bi bi-box-arrow-in-right"></i> Login', ['/site/login'], ['class' => 'btn btn-light btn-lg fw-bold shadow']) ?>
                <?php else: ?>
                    <?= Html::a('<i class="bi bi-person-circle"></i> My Profile', ['/profile/index'], ['class' => 'btn btn-light btn-lg fw-bold shadow']) ?>
                    <?= Html::a('<i class="bi bi-ticket-detailed"></i> My Bookings', ['/booking/my-bookings'], ['class' => 'btn btn-light btn-lg fw-bold shadow']) ?>
                    <?= Html::a('<i class="bi bi-box-arrow-right"></i> Logout', ['/site/logout'], ['class' => 'btn btn-light btn-lg fw-bold shadow', 'data-method' => 'post']) ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <img src="/frontend/web/images/hero-bus.png" alt="Bus" class="img-fluid" style="max-height:320px; filter: drop-shadow(0 0 20px #fff8);">
        </div>
    </div>
    
    <!-- Bus Types -->
    <div class="mb-5">
        <h2 class="mb-4 text-center fw-bold"><i class="bi bi-bus-front"></i> Our Bus Classes</h2>
        <div class="row justify-content-center">
            <?php foreach ($busTypes as $bus): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 hover-shadow position-relative">
                        <?php if ($bus['image']): ?>
                            <img src="<?= Html::encode($bus['image']) ?>" class="card-img-top" style="height:180px;object-fit:cover;" alt="<?= Html::encode($bus['type']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary"><i class="bi bi-star-fill me-1"></i><?= Html::encode($bus['type']) ?></h5>
                            <ul class="list-unstyled mb-0">
                                <?php if ($bus['type'] === 'Luxury'): ?>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> 1x2 seat layout, 30 seats</li>
                                    <li><i class="bi bi-wind text-info"></i> Air conditioning</li>
                                    <li><i class="bi bi-cup-straw text-warning"></i> Onboard refreshments</li>
                                <?php elseif ($bus['type'] === 'Semi-Luxury'): ?>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> 2x2 seat layout, 40 seats</li>
                                    <li><i class="bi bi-cup-hot text-warning"></i> Comfortable seats</li>
                                <?php else: ?>
                                    <li><i class="bi bi-check-circle-fill text-success"></i> 2x3 seat layout, 60 seats</li>
                                    <li><i class="bi bi-cash-coin text-primary"></i> Affordable fares</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Top Routes -->
    <div class="mb-5">
        <h2 class="mb-4 text-center fw-bold"><i class="bi bi-geo-alt"></i> Popular Routes</h2>
        <div class="row justify-content-center">
            <?php foreach ($topRoutes as $route): ?>
                <div class="col-md-3 mb-3">
                    <div class="card h-100 border-primary shadow-sm hover-shadow">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2 text-primary"><i class="bi bi-arrow-right-circle"></i> <?= Html::encode($route->origin) ?> → <?= Html::encode($route->destination) ?></h5>
                            <p class="mb-1">From <b><?= number_format($route->price) ?> TZS</b></p>
                            <span class="text-muted small">Login to book</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="row text-center mb-5">
        <?php if (Yii::$app->user->isGuest): ?>
            <!-- Show both stats for guests -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                        <h3 class="fw-bold text-primary mb-0" id="bookings-counter"><?= $totalBookings ?></h3>
                    <p class="mb-0"><i class="bi bi-ticket-detailed"></i> Tickets Booked</p>
                    </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                        <h3 class="fw-bold text-primary mb-0" id="buses-counter"><?= $totalBuses ?></h3>
                    <p class="mb-0"><i class="bi bi-bus-front"></i> Buses Available</p>
                </div>
            </div>
        </div>
        <?php else: ?>
            <!-- Show only buses available for logged-in users -->
            <div class="col-md-12 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="fw-bold text-primary mb-0" id="buses-counter"><?= $totalBuses ?></h3>
                        <p class="mb-0"><i class="bi bi-bus-front"></i> Buses Available</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Testimonials Carousel -->
    <div class="mb-5">
        <h2 class="mb-4 text-center fw-bold"><i class="bi bi-chat-quote"></i> What Our Customers Say</h2>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="card h-100 border-0 shadow-sm mx-auto" style="max-width: 500px;">
                        <div class="card-body text-center">
                            <p class="mb-2">"Booking was so easy and the bus was very comfortable!"</p>
                            <div class="fw-bold">Asha M., Dar es Salaam</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="card h-100 border-0 shadow-sm mx-auto" style="max-width: 500px;">
                        <div class="card-body text-center">
                            <p class="mb-2">"I loved choosing my seat and paying online. Highly recommend!"</p>
                            <div class="fw-bold">John K., Arusha</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="card h-100 border-0 shadow-sm mx-auto" style="max-width: 500px;">
                        <div class="card-body text-center">
                            <p class="mb-2">"Great service and very reliable. Will use again!"</p>
                            <div class="fw-bold">Fatma S., Mwanza</div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    
    <!-- QR Code Test Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>🔍 QR Code Test</h4>
                </div>
                <div class="card-body text-center">
                    <p>Test QR Code for Ticket Verification (Replace with actual booking ID):</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('http://192.168.100.76:8080/booking/mobile-verify?id=1') ?>" alt="Test QR Code" class="img-fluid" style="max-width: 200px;">
                    <p class="mt-2"><small class="text-muted">Scan this QR code on your mobile to test the ticket verification view</small></p>
                    <p><strong>Test URL:</strong> <code>http://192.168.100.76:8080/booking/mobile-verify?id=1</code></p>
                    
                    <div class="mt-3">
                        <?php if (Yii::$app->user->isGuest): ?>
                        <a href="<?= \yii\helpers\Url::to(['site/login']) ?>" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Create Test Booking
                        </a>
                        <p class="mt-2"><small class="text-muted">Login to create a real test booking and get a working QR code with actual data</small></p>
                        <?php else: ?>
                            <a href="<?= \yii\helpers\Url::to(['booking/bus']) ?>" class="btn btn-success">
                                <i class="bi bi-ticket-detailed"></i> Create New Booking
                            </a>
                            <a href="<?= \yii\helpers\Url::to(['booking/my-bookings']) ?>" class="btn btn-primary">
                                <i class="bi bi-list-ul"></i> View My Bookings
                            </a>
                            <p class="mt-2"><small class="text-muted">Create a new booking or view existing bookings to get QR codes with real data</small></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="pt-4 mt-5 border-top text-center text-muted small">
        <div>© <?= date('Y') ?> Dickens-OnlineTicketing. All rights reserved.</div>
        <div>Contact: dickensmanyama8@gmail.com | +255679165468</div>
    </footer>
</div>
