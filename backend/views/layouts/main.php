<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);

// Get user role for display
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
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
        'brandUrl' => ['/dashboard/index'],
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
        ['label' => '<i class="bi bi-speedometer2"></i> Dashboard', 'url' => ['/dashboard/index'], 'encode' => false],
    ];
    
    // Add role-specific menu items
    if ($user && !Yii::$app->user->isGuest) {
        // User management - for superadmin, admin, and manager
        if ($user->isSuperAdmin() || $user->isAdmin() || $user->isManager()) {
            $menuItems[] = ['label' => '<i class="bi bi-people"></i> Users', 'url' => ['/user/index'], 'encode' => false];
        }
        
        // Fleet management - all roles can view, but only admin+ can modify
        $menuItems[] = ['label' => '<i class="bi bi-bus-front"></i> Fleet', 'url' => ['/bus/index'], 'encode' => false];
        
        // Route management - all roles can view, but only admin+ can modify
        $menuItems[] = ['label' => '<i class="bi bi-geo-alt"></i> Routes', 'url' => ['/route/index'], 'encode' => false];
        
        // Debug: Show current user role in HTML comment for troubleshooting
        echo '<!-- Current user role: ' . Html::encode($user->role) . ' -->';

        // Booking management - all roles can view, but only admin+ can modify
        $menuItems[] = ['label' => '<i class="bi bi-ticket-detailed"></i> Bookings', 'url' => ['/booking/index'], 'encode' => false];
        
        // Seat Monitoring - only for admin and superadmin
        if ($user && ($user->isSuperAdmin() || $user->isAdmin())) {
            $menuItems[] = [
                'label' => '<i class="bi bi-display"></i> Seat Monitoring',
                'url' => ['/seat-monitoring/index'],
                'encode' => false
            ];
        }
        
        // Messages - only for superadmin, admin, manager, and staff (not normal users)
        if ($user && in_array($user->role, ['superadmin', 'admin', 'manager', 'staff'])) {
            $unreadMessages = \common\models\Message::find()->where(['recipient_id' => $user->id, 'is_read' => 0])->orderBy(['created_at' => SORT_DESC])->all();
            $unreadCount = count($unreadMessages);
            $fromUsers = [];
            foreach ($unreadMessages as $msg) {
                if ($msg->sender && !in_array($msg->sender->username, $fromUsers)) {
                    $fromUsers[] = $msg->sender->username;
                }
            }
            $fromText = $unreadCount > 0 ? ' from: ' . implode(', ', $fromUsers) : '';
            $badge = $unreadCount > 0 ? ' <span class="badge bg-danger">' . $unreadCount . $fromText . '</span>' : '';
            $menuItems[] = [
                'label' => '<i class="bi bi-envelope-fill"></i> <b>Messages</b>' . $badge,
                'url' => ['/message/index'],
                'encode' => false
            ];
        }
        
        // Additional menu items for managers and above
        if ($user && ($user->isSuperAdmin() || $user->isAdmin() || $user->isManager())) {
            // Main Reports link - clickable
            $menuItems[] = [
                'label' => '<i class="bi bi-graph-up"></i> Reports',
                'url' => ['/reports/index'],
                'encode' => false,
            ];
            
            // Add specific admin reports for super admin and admin
            if ($user && ($user->isSuperAdmin() || $user->isAdmin())) {
                $menuItems[] = [
                    'label' => '<i class="bi bi-shield-check"></i> Admin Reports',
                    'url' => $user->isSuperAdmin() ? ['/reports/super-admin'] : ['/reports/admin'],
                    'encode' => false,
                ];
            }
            
            // Backup management for admin and superadmin only
            if ($user && ($user->isSuperAdmin() || $user->isAdmin())) {
                $menuItems[] = [
                    'label' => '<i class="bi bi-hdd-network"></i> Backups',
                    'url' => ['/backup/index'],
                    'encode' => false
                ];
            }
        }

        // Parcels menu item
        $menuItems[] = [
            'label' => '<i class="bi bi-box-seam"></i> Parcels',
            'url' => ['/parcel/index'],
            'encode' => false
        ];
    }
    
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
    <div class="container-fluid">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 text-muted advanced-footer glassy-footer shadow-lg rounded-top-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 fw-bold text-gradient" style="font-size:1.2rem;letter-spacing:1px;">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns properly
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Simple and reliable mobile menu behavior
    var navbarCollapse = document.querySelector('.navbar-collapse');
    var navbarToggler = document.querySelector('.navbar-toggler');
    
    // Close mobile menu when clicking on nav links (except dropdown toggles)
    var navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            // Don't close if it's a dropdown toggle
            if (this.getAttribute('data-bs-toggle') === 'dropdown') {
                return;
            }
            
            // Close mobile menu
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                if (window.bootstrap && bootstrap.Collapse) {
                    var collapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (collapse) {
                        collapse.hide();
                    }
                }
            }
        });
    });
    
    // Close mobile menu when clicking on dropdown items
    var dropdownItems = document.querySelectorAll('.navbar-nav .dropdown-item');
    dropdownItems.forEach(function(item) {
        item.addEventListener('click', function() {
            // Close mobile menu
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                if (window.bootstrap && bootstrap.Collapse) {
                    var collapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (collapse) {
                        collapse.hide();
                    }
                }
            }
        });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 992) {
            var isClickInsideNavbar = navbarCollapse && navbarCollapse.contains(event.target);
            var isClickOnToggler = navbarToggler && navbarToggler.contains(event.target);
            
            if (!isClickInsideNavbar && !isClickOnToggler && navbarCollapse && navbarCollapse.classList.contains('show')) {
                if (window.bootstrap && bootstrap.Collapse) {
                    var collapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (collapse) {
                        collapse.hide();
                    }
                }
            }
        }
    });
    
    // Close mobile menu on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && navbarCollapse && navbarCollapse.classList.contains('show')) {
            if (window.bootstrap && bootstrap.Collapse) {
                var collapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (collapse) {
                    collapse.hide();
                }
            }
        }
    });

    // Debug: Log dropdown elements to console
    console.log('Dropdown elements found:', dropdownElementList.length);
    console.log('Profile dropdown button:', document.getElementById('profileDropdown'));
});
</script>

<!-- Mode Toggle Functionality -->
<script>
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

<style>
/* Dark mode styles for backend */
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

/* Advanced footer styles to match header */
.advanced-footer {
    background: linear-gradient(90deg, #232526 0%, #414345 100%) !important;
    box-shadow: 0 -4px 24px 0 rgba(0,0,0,0.12), 0 -1.5px 4px 0 rgba(0,0,0,0.10);
    border-top-left-radius: 1.5rem;
    border-top-right-radius: 1.5rem;
    min-height: 64px;
    backdrop-filter: blur(8px);
}

.glossy-footer {
    background: rgba(34, 34, 34, 0.85) !important;
    backdrop-filter: blur(8px);
}

.advanced-footer .text-gradient {
    background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: brandGlow 3s ease-in-out infinite alternate;
}

.advanced-footer p {
    color: #ffffff !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.advanced-footer i {
    color: #00c6ff;
    transition: all 0.3s ease;
}

.advanced-footer i:hover {
    color: #ffffff;
    transform: scale(1.2);
}

/* Light mode footer text improvements */
.advanced-footer .text-muted {
    color: #e0e0e0 !important;
    font-weight: 500;
}

.advanced-footer span {
    color: #cccccc !important;
    font-weight: 400;
}

/* Make contact info more visible */
.advanced-footer p {
    color: #00c6ff !important;
    font-weight: 500;
}

.advanced-footer p i {
    color: #ffffff !important;
}

.advanced-footer p span {
    color: #ffffff !important;
    opacity: 0.8;
}

/* Specific styling for contact information */
.advanced-footer .col-md-6:last-child p {
    color: #333333 !important;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(255,255,255,0.3);
}

.advanced-footer .col-md-6:last-child p i {
    color: #00c6ff !important;
}

.advanced-footer .col-md-6:last-child p span {
    color: #666666 !important;
    opacity: 1;
    font-weight: 500;
}

/* Brand name should remain gradient */
.advanced-footer .col-md-6:first-child p {
    background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: brandGlow 3s ease-in-out infinite alternate;
}

/* Dark mode footer styles */
.dark-mode .advanced-footer {
    background: linear-gradient(90deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    border-top: 1px solid #333;
}

.dark-mode .glossy-footer {
    background: rgba(26, 26, 26, 0.9) !important;
}

.dark-mode .advanced-footer p {
    color: #ffffff !important;
}

.dark-mode .advanced-footer i {
    color: #00c6ff;
}

.dark-mode .advanced-footer i:hover {
    color: #ffffff;
}

.dark-mode .advanced-footer .text-muted {
    color: #cccccc !important;
}

.dark-mode .advanced-footer span {
    color: #999999 !important;
}

/* Responsive footer design */
@media (max-width: 767.98px) {
    .advanced-footer {
        border-radius: 1.5rem 1.5rem 0 0;
        text-align: center;
    }
    
    .advanced-footer .col-md-6 {
        margin-bottom: 0.5rem;
    }
    
    .advanced-footer .text-md-end {
        text-align: center !important;
    }
}
</style>

<!-- Custom CSS for responsive navbar -->
<style>
@media (max-width: 991.98px) {
    .navbar-nav {
        padding: 1rem 0;
    }
    .navbar-nav .nav-link {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .navbar-nav .nav-link:last-child {
        border-bottom: none;
    }
    .dropdown-menu {
        border: none;
        background: rgba(33, 37, 41, 0.95);
        backdrop-filter: blur(10px);
    }
    .dropdown-item {
        color: rgba(255,255,255,0.8);
    }
    .dropdown-item:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }
}

/* Ensure navbar is always visible */
.navbar {
    z-index: 1030;
}

/* Smooth transitions */
.navbar-collapse {
    transition: all 0.3s ease;
}

/* Custom hamburger animation */
.navbar-toggler {
    border: none;
    padding: 0.25rem 0.5rem;
    transition: all 0.15s ease;
}

.navbar-toggler:focus {
    box-shadow: none;
}

.navbar-toggler-icon {
    transition: transform 0.3s ease;
}

.navbar-toggler[aria-expanded="true"] .navbar-toggler-icon {
    transform: rotate(90deg);
}

/* Visual feedback for button clicks */
.btn-clicked {
    transform: scale(0.95);
    opacity: 0.8;
}

.double-clicked {
    transform: scale(0.9);
    background-color: rgba(255, 255, 255, 0.2) !important;
}

/* Profile button visual feedback */
#profileDropdown.btn-clicked {
    transform: scale(0.95);
    opacity: 0.8;
}

#profileDropdown.double-clicked {
    transform: scale(0.9);
    background-color: rgba(255, 255, 255, 0.1) !important;
}
</style>

<!-- Custom JavaScript for menu auto-close -->
<script>
$(document).ready(function() {
    // Variables for double-click detection
    var hamburgerClickCount = 0;
    var hamburgerClickTimer = null;
    var profileClickCount = 0;
    var profileClickTimer = null;
    
    // Auto-close navbar when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar').length) {
            $('.navbar-collapse').collapse('hide');
        }
    });
    
    // Auto-close navbar when clicking on a menu item
    $('.navbar-nav .nav-link').on('click', function() {
        $('.navbar-collapse').collapse('hide');
    });
    
    // Hamburger button click handling with double-click detection
    $('.navbar-toggler').on('click', function(e) {
        // Ensure we're clicking exactly on the hamburger button
        if ($(e.target).hasClass('navbar-toggler') || $(e.target).hasClass('navbar-toggler-icon')) {
            hamburgerClickCount++;
            
            // Add visual feedback for click
            $(this).addClass('btn-clicked');
            setTimeout(() => $(this).removeClass('btn-clicked'), 150);
            
            if (hamburgerClickCount === 1) {
                // First click - wait for potential double-click
                hamburgerClickTimer = setTimeout(function() {
                    // Single click - let Bootstrap handle the toggle
                    hamburgerClickCount = 0;
                }, 300); // Wait 300ms for double-click
            } else if (hamburgerClickCount === 2) {
                // Double click detected - force close menu
                clearTimeout(hamburgerClickTimer);
                e.preventDefault();
                e.stopPropagation();
                
                // Force close the menu
                $('.navbar-collapse').removeClass('show');
                $('.navbar-toggler').attr('aria-expanded', 'false');
                
                hamburgerClickCount = 0;
                
                // Visual feedback for double-click
                $('.navbar-toggler').addClass('double-clicked');
                setTimeout(() => $('.navbar-toggler').removeClass('double-clicked'), 200);
            }
        }
    });
    
    // Auto-close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Close dropdown when clicking on dropdown item
    $('.dropdown-item').on('click', function() {
        $(this).closest('.dropdown-menu').removeClass('show');
    });
    
    // Profile dropdown click handling with double-click detection
    $('#profileDropdown').on('click', function(e) {
        e.preventDefault();
        profileClickCount++;
        
        // Add visual feedback for click
        $(this).addClass('btn-clicked');
        setTimeout(() => $(this).removeClass('btn-clicked'), 150);
        
        if (profileClickCount === 1) {
            // First click - wait for potential double-click
            profileClickTimer = setTimeout(function() {
                // Single click - toggle dropdown
                var $dropdownMenu = $(this).next('.dropdown-menu');
                $('.dropdown-menu').not($dropdownMenu).removeClass('show');
                $dropdownMenu.toggleClass('show');
                profileClickCount = 0;
            }.bind(this), 300); // Wait 300ms for double-click
        } else if (profileClickCount === 2) {
            // Double click detected
            clearTimeout(profileClickTimer);
            $(this).next('.dropdown-menu').removeClass('show');
            profileClickCount = 0;
            
            // Visual feedback for double-click
            $(this).addClass('double-clicked');
            setTimeout(() => $(this).removeClass('double-clicked'), 200);
        }
    });
});
</script>
</body>
</html>
<?php $this->endPage();
