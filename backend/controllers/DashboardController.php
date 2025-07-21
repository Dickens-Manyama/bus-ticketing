<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Bus;
use common\models\Route;
use common\models\Booking;

class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'test', 'debug'],
                'rules' => [
                    [
                        'actions' => ['index', 'test', 'debug'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager());
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
                    }
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // Debug: Log user information
        Yii::info("Dashboard access - User: {$user->username}, Role: {$user->role}, ID: {$user->id}", 'dashboard');
        
        // Show welcome message for users who logged in through frontend
        if (Yii::$app->session->hasFlash('success')) {
            // Keep the existing flash message
        } else {
            $roleLabel = $this->getRoleLabel($user->role);
            Yii::$app->session->setFlash('success', "Welcome to your {$roleLabel} Dashboard, {$user->username}!");
        }
        
        // Debug: Log which dashboard will be rendered
        Yii::info("Rendering dashboard for role: {$user->role}", 'dashboard');
        
        // Get role-specific data
        switch ($user->role) {
            case 'superadmin':
                Yii::info("Rendering SuperAdmin dashboard", 'dashboard');
                return $this->renderSuperAdminDashboard();
            case 'admin':
                Yii::info("Rendering Admin dashboard", 'dashboard');
                return $this->renderAdminDashboard();
            case 'manager':
                Yii::info("Rendering Manager dashboard", 'dashboard');
                return $this->renderManagerDashboard();
            case 'staff':
                Yii::info("Rendering Staff dashboard", 'dashboard');
                return $this->renderStaffDashboard();
            default:
                Yii::info("Rendering default Staff dashboard for role: {$user->role}", 'dashboard');
                return $this->renderStaffDashboard();
        }
    }

    public function actionTest()
    {
        $user = Yii::$app->user->identity;
        return $this->render('test', [
            'user' => $user,
            'role' => $user->role,
            'isSuperAdmin' => $user->isSuperAdmin(),
            'isAdmin' => $user->isAdmin(),
            'isManager' => $user->isManager(),
            'isStaff' => $user->isStaff(),
        ]);
    }

    public function actionDebug()
    {
        $user = Yii::$app->user->identity;
        $data = [
            'app_id' => Yii::$app->id,
            'user_guest' => Yii::$app->user->isGuest,
            'user_identity' => $user ? $user->username : 'None',
            'user_role' => $user ? $user->role : 'None',
            'user_id' => $user ? $user->id : 'None',
            'current_url' => Yii::$app->request->url,
            'base_url' => Yii::$app->request->baseUrl,
            'home_url' => Yii::$app->homeUrl,
            'default_route' => Yii::$app->defaultRoute,
            'database_connected' => false,
            'session_name' => Yii::$app->session->getName(),
            'session_id' => Yii::$app->session->getId(),
            'user_cookie_name' => Yii::$app->user->identityCookie['name'] ?? 'Not set',
            'role_methods' => [
                'isSuperAdmin' => $user ? $user->isSuperAdmin() : false,
                'isAdmin' => $user ? $user->isAdmin() : false,
                'isManager' => $user ? $user->isManager() : false,
                'isStaff' => $user ? $user->isStaff() : false,
            ],
        ];
        
        try {
            $test = Yii::$app->db->createCommand('SELECT 1')->queryScalar();
            $data['database_connected'] = $test == 1;
        } catch (\Exception $e) {
            $data['database_error'] = $e->getMessage();
        }
        
        return $this->render('debug', ['data' => $data]);
    }

    private function renderSuperAdminDashboard()
    {
        // Superadmin gets full system overview with all analytics
        $totalUsers = User::find()->count();
        $totalBookings = Booking::find()->count();
        $totalRevenue = Booking::find()->joinWith('route')->sum('route.price');
        $totalBuses = Bus::find()->count();
        $totalRoutes = Route::find()->count();
        
        // User statistics by role
        $usersByRole = [];
        $roles = ['superadmin', 'admin', 'manager', 'staff', 'user'];
        foreach ($roles as $role) {
            $usersByRole[$role] = User::find()->where(['role' => $role])->count();
        }
        
        // System health metrics - handle case where status column might not exist
        try {
            $activeBookings = Booking::find()->where(['status' => 'active'])->count();
            $pendingBookings = Booking::find()->where(['status' => 'pending'])->count();
            $completedBookings = Booking::find()->where(['status' => 'completed'])->count();
        } catch (\Exception $e) {
            // If status column doesn't exist, use total bookings as active
            $activeBookings = $totalBookings;
            $pendingBookings = 0;
            $completedBookings = 0;
        }
        
        // Recent activities
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(10)->all();
        $recentUsers = User::find()->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        
        // Analytics data
        $analyticsData = $this->getAnalyticsData();
        
        return $this->render('superadmin', [
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'usersByRole' => $usersByRole,
            'activeBookings' => $activeBookings,
            'pendingBookings' => $pendingBookings,
            'completedBookings' => $completedBookings,
            'recentBookings' => $recentBookings,
            'recentUsers' => $recentUsers,
            'analyticsData' => $analyticsData,
        ]);
    }

    private function renderAdminDashboard()
    {
        // Admin gets business-focused dashboard
        $totalUsers = User::find()->where(['role' => 'user'])->count();
        $totalBookings = Booking::find()->count();
        $totalRevenue = Booking::find()->joinWith('route')->sum('route.price');
        $totalBuses = Bus::find()->count();
        $totalRoutes = Route::find()->count();
        
        // Business metrics
        $monthlyRevenue = $this->getMonthlyRevenue();
        $topRoutes = $this->getTopRoutes();
        $bookingTrends = $this->getBookingTrends();
        
        // Recent bookings
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(8)->all();
        
        return $this->render('admin', [
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'monthlyRevenue' => $monthlyRevenue,
            'topRoutes' => $topRoutes,
            'bookingTrends' => $bookingTrends,
            'recentBookings' => $recentBookings,
        ]);
    }

    private function renderManagerDashboard()
    {
        // Manager gets operational dashboard
        $totalBookings = Booking::find()->count();
        
        // Handle case where status column might not exist
        try {
            $pendingBookings = Booking::find()->where(['status' => 'pending'])->count();
            $activeBookings = Booking::find()->where(['status' => 'active'])->count();
        } catch (\Exception $e) {
            $pendingBookings = 0;
            $activeBookings = $totalBookings;
        }
        
        $totalBuses = Bus::find()->count();
        $totalRoutes = Route::find()->count();
        
        // Operational metrics
        $todayBookings = Booking::find()->where(['>=', 'created_at', strtotime('today')])->count();
        $weekBookings = Booking::find()->where(['>=', 'created_at', strtotime('-1 week')])->count();
        $monthBookings = Booking::find()->where(['>=', 'created_at', strtotime('-1 month')])->count();
        
        // Route performance
        $routePerformance = $this->getRoutePerformance();
        
        // Recent bookings
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(6)->all();
        
        return $this->render('manager', [
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'activeBookings' => $activeBookings,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'todayBookings' => $todayBookings,
            'weekBookings' => $weekBookings,
            'monthBookings' => $monthBookings,
            'routePerformance' => $routePerformance,
            'recentBookings' => $recentBookings,
        ]);
    }

    private function renderStaffDashboard()
    {
        // Staff gets basic operational view
        $totalBookings = Booking::find()->count();
        $todayBookings = Booking::find()->where(['>=', 'created_at', strtotime('today')])->count();
        
        // Handle case where status column might not exist
        try {
            $pendingBookings = Booking::find()->where(['status' => 'pending'])->count();
        } catch (\Exception $e) {
            $pendingBookings = 0;
        }
        
        $totalBuses = Bus::find()->count();
        $totalRoutes = Route::find()->count();
        
        // Recent bookings
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(5)->all();
        
        // Quick actions data
        try {
            $availableBuses = Bus::find()->where(['status' => 'active'])->count();
        } catch (\Exception $e) {
            $availableBuses = $totalBuses; // If no status column, assume all buses are available
        }
        
        try {
            $activeRoutes = Route::find()->where(['status' => 'active'])->count();
        } catch (\Exception $e) {
            $activeRoutes = $totalRoutes; // If no status column, assume all routes are active
        }
        
        return $this->render('staff', [
            'totalBookings' => $totalBookings,
            'todayBookings' => $todayBookings,
            'pendingBookings' => $pendingBookings,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'recentBookings' => $recentBookings,
            'availableBuses' => $availableBuses,
            'activeRoutes' => $activeRoutes,
        ]);
    }

    private function getAnalyticsData()
    {
        // Bookings per month
        $bookingsPerMonth = [];
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $months[] = $month;
            $bookingsPerMonth[$month] = 0;
        }
        $rows = (new \yii\db\Query())
            ->select(["month" => "TO_CHAR(TO_TIMESTAMP(created_at), 'YYYY-MM')", "count" => "COUNT(*)"])
            ->from('booking')
            ->groupBy(["month"])
            ->all();
        foreach ($rows as $row) {
            if (isset($bookingsPerMonth[$row['month']])) {
                $bookingsPerMonth[$row['month']] = (int)$row['count'];
            }
        }
        
        // Revenue per route
        $revenuePerRoute = [];
        $routes = Route::find()->all();
        foreach ($routes as $route) {
            $revenue = (new \yii\db\Query())
                ->from('booking')
                ->where(['route_id' => $route->id])
                ->count() * $route->price;
            $revenuePerRoute[$route->origin . ' → ' . $route->destination] = (float)$revenue;
        }
        
        // User registrations per month
        $usersPerMonth = [];
        foreach ($months as $month) {
            $usersPerMonth[$month] = 0;
        }
        $userRows = (new \yii\db\Query())
            ->select(["month" => "TO_CHAR(TO_TIMESTAMP(created_at), 'YYYY-MM')", "count" => "COUNT(*)"])
            ->from('user')
            ->groupBy(["month"])
            ->all();
        foreach ($userRows as $row) {
            if (isset($usersPerMonth[$row['month']])) {
                $usersPerMonth[$row['month']] = (int)$row['count'];
            }
        }
        
        return [
            'months' => $months,
            'bookingsPerMonth' => array_values($bookingsPerMonth),
            'revenuePerRoute' => $revenuePerRoute,
            'usersPerMonth' => array_values($usersPerMonth),
            'routeLabels' => array_keys($revenuePerRoute),
        ];
    }

    private function getMonthlyRevenue()
    {
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $revenue = (new \yii\db\Query())
                ->from('booking b')
                ->innerJoin('route r', 'b.route_id = r.id')
                ->where(['>=', 'b.created_at', strtotime($month . '-01')])
                ->andWhere(['<', 'b.created_at', strtotime($month . '-01 +1 month')])
                ->sum('r.price') ?? 0;
            $monthlyRevenue[$month] = (float)$revenue;
        }
        return $monthlyRevenue;
    }

    private function getTopRoutes()
    {
        return (new \yii\db\Query())
            ->select(['r.origin', 'r.destination', 'COUNT(b.id) as booking_count', 'SUM(r.price) as total_revenue'])
            ->from('route r')
            ->leftJoin('booking b', 'r.id = b.route_id')
            ->groupBy(['r.id', 'r.origin', 'r.destination'])
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(5)
            ->all();
    }

    private function getBookingTrends()
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = Booking::find()->where(['>=', 'created_at', strtotime($date)])
                ->andWhere(['<', 'created_at', strtotime($date . ' +1 day')])->count();
            $trends[$date] = $count;
        }
        return $trends;
    }

    private function getRoutePerformance()
    {
        return (new \yii\db\Query())
            ->select(['r.origin', 'r.destination', 'COUNT(b.id) as bookings', 'AVG(r.price) as avg_price'])
            ->from('route r')
            ->leftJoin('booking b', 'r.id = b.route_id')
            ->groupBy(['r.id', 'r.origin', 'r.destination'])
            ->orderBy(['bookings' => SORT_DESC])
            ->limit(10)
            ->all();
    }

    /**
     * Gets a human-readable label for the user role
     * 
     * @param string $role
     * @return string
     */
    private function getRoleLabel($role)
    {
        $labels = [
            'superadmin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'staff' => 'Staff',
            'user' => 'User',
        ];
        
        return $labels[$role] ?? ucfirst($role);
    }
} 