<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\models\Booking;
use common\models\Bus;
use common\models\Route;
use common\models\User;

class ReportsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager());
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Debug: Log the action
        Yii::info('Reports index action called', 'reports');
        
        // Check if user is properly authenticated
        if (Yii::$app->user->isGuest) {
            Yii::error('User is guest in reports controller', 'reports');
            return $this->redirect(['/site/login']);
        }
        
        $user = Yii::$app->user->identity;
        Yii::info('User accessing reports: ' . $user->username . ' (Role: ' . $user->role . ')', 'reports');
        
        return $this->render('index');
    }

    public function actionBookings()
    {
        return $this->render('bookings');
    }

    public function actionFleet()
    {
        return $this->render('fleet');
    }

    public function actionRevenue()
    {
        return $this->render('revenue');
    }

    public function actionRoute()
    {
        return $this->render('route');
    }

    public function actionUsers()
    {
        return $this->render('users');
    }

    public function actionSuperAdmin()
    {
        // Comprehensive super admin report
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
        
        // Booking statistics
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $statusCounts = [];
        foreach ($statuses as $status) {
            $statusCounts[$status] = Booking::find()->where(['status' => $status])->count();
        }
        
        // Ticket verification statistics
        $activeTickets = Booking::find()->where(['ticket_status' => 'active'])->count();
        $usedTickets = Booking::find()->where(['ticket_status' => 'used'])->count();
        $expiredTickets = Booking::find()->where(['ticket_status' => 'expired'])->count();
        
        // Recent data
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(20)->all();
        $recentUsers = User::find()->orderBy(['created_at' => SORT_DESC])->limit(10)->all();
        
        return $this->render('superadmin', [
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'usersByRole' => $usersByRole,
            'statusCounts' => $statusCounts,
            'activeTickets' => $activeTickets,
            'usedTickets' => $usedTickets,
            'expiredTickets' => $expiredTickets,
            'recentBookings' => $recentBookings,
            'recentUsers' => $recentUsers,
        ]);
    }

    public function actionAdmin()
    {
        // Business-focused admin report
        $totalUsers = User::find()->where(['role' => 'user'])->count();
        $totalBookings = Booking::find()->count();
        $totalRevenue = Booking::find()->joinWith('route')->sum('route.price');
        $totalBuses = Bus::find()->count();
        $totalRoutes = Route::find()->count();
        
        // Business metrics
        $monthlyRevenue = $this->getMonthlyRevenue();
        $topRoutes = $this->getTopRoutes();
        $bookingTrends = $this->getBookingTrends();
        
        // Booking statistics
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $statusCounts = [];
        foreach ($statuses as $status) {
            $statusCounts[$status] = Booking::find()->where(['status' => $status])->count();
        }
        
        // Ticket verification statistics
        $activeTickets = Booking::find()->where(['ticket_status' => 'active'])->count();
        $usedTickets = Booking::find()->where(['ticket_status' => 'used'])->count();
        $expiredTickets = Booking::find()->where(['ticket_status' => 'expired'])->count();
        
        // Recent bookings
        $recentBookings = Booking::find()->orderBy(['created_at' => SORT_DESC])->limit(15)->all();
        
        return $this->render('admin', [
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalBuses' => $totalBuses,
            'totalRoutes' => $totalRoutes,
            'monthlyRevenue' => $monthlyRevenue,
            'topRoutes' => $topRoutes,
            'bookingTrends' => $bookingTrends,
            'statusCounts' => $statusCounts,
            'activeTickets' => $activeTickets,
            'usedTickets' => $usedTickets,
            'expiredTickets' => $expiredTickets,
            'recentBookings' => $recentBookings,
        ]);
    }

    private function getMonthlyRevenue()
    {
        $revenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));
            $revenue[$monthName] = Booking::find()
                ->joinWith('route')
                ->where(['>=', 'booking.created_at', strtotime($month . '-01')])
                ->andWhere(['<', 'booking.created_at', strtotime($month . '-01 +1 month')])
                ->sum('route.price') ?? 0;
        }
        return $revenue;
    }

    private function getTopRoutes()
    {
        return Route::find()
            ->select(['route.*', 'COUNT(booking.id) as booking_count', 'SUM(route.price) as total_revenue'])
            ->joinWith('bookings')
            ->groupBy(['route.id'])
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
    }

    private function getBookingTrends()
    {
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dayName = date('D', strtotime($date));
            $trends[$dayName] = Booking::find()
                ->where(['>=', 'created_at', strtotime($date . ' 00:00:00')])
                ->andWhere(['<', 'created_at', strtotime($date . ' 23:59:59')])
                ->count();
        }
        return $trends;
    }
} 