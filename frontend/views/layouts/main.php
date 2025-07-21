<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

// Get user info for display
$user = Yii::$app->user->identity;
$roleDisplay = $user ? ucfirst($user->role) : '';
$roleIcon = '';
if ($user) {
    switch ($user->role) {
        case 'superadmin':
            $roleIcon = 'bi-shield-check';
            break;
        case 'admin':
            $roleIcon = 'bi-person-badge';
            break;
        case 'manager':
            $roleIcon = 'bi-person-workspace';
            break;
        case 'staff':
            $roleIcon = 'bi-person';
            break;
        default:
            $roleIcon = 'bi-person';
    }
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com;">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => 'Dickens-OnlineTicketing',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-dark bg-dark fixed-top',
        ],
        'collapseOptions' => [
            'class' => 'navbar-collapse',
            'id' => 'navbarNav',
        ],
    ]);
    
    // Build menu items array
    $menuItems = [
        ['label' => '<i class="bi bi-house"></i> Home', 'url' => ['/site/index'], 'encode' => false],
    ];
    
    // Add role-specific menu items for frontend
    if ($user && !Yii::$app->user->isGuest) {
        // Booking menu for all logged-in users
        $menuItems[] = ['label' => '<i class="bi bi-ticket-detailed"></i> Book Ticket', 'url' => ['/booking/bus'], 'encode' => false];
        $menuItems[] = ['label' => '<i class="bi bi-receipt"></i> My Receipts', 'url' => ['/booking/my-bookings'], 'encode' => false];
        
        // Users management - for admin users only
        if ($user->isSuperAdmin() || $user->isAdmin() || $user->isManager()) {
            $menuItems[] = ['label' => '<i class="bi bi-people"></i> Users', 'url' => ['/user/index'], 'encode' => false];
        }
        
        // Parcel delivery services - for all logged-in users
        $menuItems[] = ['label' => '<i class="bi bi-box-seam"></i> Send Parcel', 'url' => ['/parcel/index'], 'encode' => false];
        
        // Debug: Show current user role in HTML comment for troubleshooting
        echo '<!-- Current user role: ' . Html::encode($user->role) . ' -->';
    }
    
    // Add static pages
    $menuItems[] = ['label' => '<i class="bi bi-info-circle"></i> About', 'url' => ['/site/about'], 'encode' => false];
    $menuItems[] = ['label' => '<i class="bi bi-envelope"></i> Contact', 'url' => ['/site/contact'], 'encode' => false];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    }
    
    // Render the navbar with responsive collapse
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
        'items' => $menuItems,
    ]);
    
    // Right side items (user profile and mode toggle)
    echo '<div class="d-flex align-items-center">';
    
    if (Yii::$app->user->isGuest) {
        echo Html::tag('div', Html::a('Login', ['/site/login'], ['class' => ['btn btn-link login text-decoration-none']]), ['class' => ['d-flex']]);
        echo Html::tag('div', Html::a('Sign Up', ['/site/signup'], ['class' => ['btn btn-link text-decoration-none ms-2']]), ['class' => ['d-flex']]);
    } else {
        // Profile dropdown menu
        echo '<div class="dropdown me-2">';
        echo '<button class="btn btn-link text-light text-decoration-none dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; border: none; background: transparent; padding: 8px 12px;">';
        echo '<i class="bi ' . $roleIcon . ' me-1"></i> ' . Yii::$app->user->identity->username . ' (' . $roleDisplay . ')';
        echo '</button>';
        echo '<ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="profileDropdown" style="min-width: 200px;">';
        echo '<li><h6 class="dropdown-header"><i class="bi bi-person-circle me-2"></i>Profile Menu</h6></li>';
        echo '<li><a class="dropdown-item" href="' . Url::to(['/profile/index']) . '"><i class="bi bi-person me-2"></i>View Profile</a></li>';
        echo '<li><a class="dropdown-item" href="' . Url::to(['/profile/update']) . '"><i class="bi bi-person-gear me-2"></i>Edit Profile</a></li>';
        echo '<li><hr class="dropdown-divider"></li>';
        echo '<li>' . Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline']) 
             . Html::submitButton('<i class="bi bi-box-arrow-right me-2"></i>Logout', ['class' => 'dropdown-item border-0 bg-transparent text-danger'])
             . Html::endForm() . '</li>';
        echo '</ul>';
        echo '</div>';
    }
    
    // Mode selection button
    echo '<button id="modeToggle" class="btn btn-outline-light" title="Toggle light/dark mode" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">';
    echo '<i class="bi bi-moon" id="modeIcon"></i>';
    echo '</button>';
    
    echo '</div>'; // End d-flex
    
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0" style="margin-top: 76px;">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">
                    <i class="bi bi-bus-front me-2"></i>Dickens-OnlineTicketing
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <?php if ($user): ?>
                        <i class="bi <?= $roleIcon ?> me-2"></i> <?= $roleDisplay ?>
                    <?php endif; ?>
                    <span class="mx-2">|</span>
                    <i class="bi bi-envelope me-1"></i> dickensmanyama8@gmail.com
                    <span class="mx-2">|</span>
                    <i class="bi bi-telephone me-1"></i> +255679165468
                    <span class="mx-2">|</span>
                    <?= Yii::powered() ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dark Mode Styles -->
<style>
/* Dark mode styles */
.dark-mode {
    background: #1a1a1a !important;
    color: #ffffff !important;
}

.dark-mode .navbar {
    background: linear-gradient(90deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    border-bottom: 1px solid #333;
}

.dark-mode .card {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .dropdown-menu {
    background: rgba(45, 45, 45, 0.95) !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .dropdown-item {
    color: #ffffff !important;
}

.dark-mode .dropdown-item:hover {
    background: linear-gradient(90deg, #00c6ff, #0072ff) !important;
    color: white !important;
}

.dark-mode .btn-link {
    color: #ffffff !important;
}

.dark-mode .btn-outline-light {
    color: #ffffff !important;
    border-color: #666 !important;
}

.dark-mode .btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

.dark-mode .footer {
    background: #2d2d2d !important;
    color: #cccccc !important;
}

.dark-mode .text-muted {
    color: #cccccc !important;
}

.dark-mode .form-control,
.dark-mode .form-select {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .form-control:focus,
.dark-mode .form-select:focus {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #00c6ff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 198, 255, 0.25) !important;
}

.dark-mode .table {
    color: #ffffff !important;
}

.dark-mode .table-striped > tbody > tr:nth-of-type(odd) {
    background: rgba(45, 45, 45, 0.5) !important;
}

.dark-mode .alert {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .list-group-item {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .modal-content {
    background: #2d2d2d !important;
    color: #ffffff !important;
    border-color: #444 !important;
}

.dark-mode .modal-header {
    border-bottom-color: #444 !important;
}

.dark-mode .modal-footer {
    border-top-color: #444 !important;
}

.dark-mode .breadcrumb {
    background: #2d2d2d !important;
    color: #cccccc !important;
}

.dark-mode .breadcrumb-item + .breadcrumb-item::before {
    color: #666 !important;
}

/* Mode toggle button styles */
#modeToggle {
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: transparent;
}

#modeToggle:hover {
    transform: scale(1.1);
    border-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
}

.dark-mode #modeToggle {
    border-color: rgba(255, 255, 255, 0.5);
}

.dark-mode #modeToggle:hover {
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 15px rgba(0, 198, 255, 0.3);
}

/* Smooth transitions for all elements */
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

/* Ensure mode toggle button is always visible */
#modeToggle {
    position: relative;
    z-index: 1000;
}

/* Mobile responsive dark mode */
@media (max-width: 991.98px) {
    .dark-mode .navbar-collapse {
        background: rgba(26, 26, 26, 0.95) !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns properly
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Mobile menu behavior
    var navbarCollapse = document.querySelector('.navbar-collapse');
    var navbarToggler = document.querySelector('.navbar-toggler');
    
    // Close mobile menu when clicking on nav links
    var navbarLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navbarLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) { // lg breakpoint
                var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    hide: true
                });
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
            var isClickInsideNavbar = navbarCollapse && navbarCollapse.contains(event.target);
            var isClickOnToggler = navbarToggler && navbarToggler.contains(event.target);
            
            if (!isClickInsideNavbar && !isClickOnToggler && navbarCollapse && navbarCollapse.classList.contains('show')) {
                var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    hide: true
                });
            }
        }
    });
    
    // Close mobile menu on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && navbarCollapse && navbarCollapse.classList.contains('show')) {
            var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                hide: true
            });
        }
    });
    
    // Double-click to close mobile menu
    var lastClickTime = 0;
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function(event) {
            var currentTime = new Date().getTime();
            var timeDiff = currentTime - lastClickTime;
            
            if (timeDiff < 300 && timeDiff > 0) { // Double click detected (within 300ms)
                event.preventDefault();
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                        hide: true
                    });
                }
                lastClickTime = 0; // Reset
            } else {
                lastClickTime = currentTime;
            }
        });
    }
    
    // Touch events for mobile
    if (navbarToggler) {
        var touchStartTime = 0;
        var touchEndTime = 0;
        
        navbarToggler.addEventListener('touchstart', function(e) {
            touchStartTime = new Date().getTime();
        });
        
        navbarToggler.addEventListener('touchend', function(e) {
            touchEndTime = new Date().getTime();
            var touchDuration = touchEndTime - touchStartTime;
            
            // If touch duration is very short, it might be a double tap
            if (touchDuration < 100) {
                var currentTime = new Date().getTime();
                var timeDiff = currentTime - lastClickTime;
                
                if (timeDiff < 300 && timeDiff > 0) { // Double tap detected
                    e.preventDefault();
                    if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                        var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            hide: true
                        });
                    }
                    lastClickTime = 0;
                } else {
                    lastClickTime = currentTime;
                }
            }
        });
    }

    // Debug: Log dropdown elements to console
    console.log('Dropdown elements found:', dropdownElementList.length);
    console.log('Profile dropdown button:', document.getElementById('profileDropdown'));
});

// Mode toggle functionality
(function() {
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const body = document.body;
    const darkClass = 'dark-mode';
    
    // Check for saved mode preference or default to light mode
    const savedMode = localStorage.getItem('theme') || 'light';
    
    // Apply saved mode on page load
    if (savedMode === 'dark') {
        body.classList.add(darkClass);
        modeIcon.className = 'bi bi-sun';
    } else {
        body.classList.remove(darkClass);
        modeIcon.className = 'bi bi-moon';
    }
    
    // Toggle mode on button click
    modeToggle.addEventListener('click', function() {
        if (body.classList.contains(darkClass)) {
            // Switch to light mode
            body.classList.remove(darkClass);
            localStorage.setItem('theme', 'light');
            modeIcon.className = 'bi bi-moon';
            console.log('Switched to light mode');
        } else {
            // Switch to dark mode
            body.classList.add(darkClass);
            localStorage.setItem('theme', 'dark');
            modeIcon.className = 'bi bi-sun';
            console.log('Switched to dark mode');
        }
    });
    
    // Add smooth transition for mode changes
    body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
})();
</script>
</body>
</html>
<?php $this->endPage();
