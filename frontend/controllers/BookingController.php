<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\Bus;
use common\models\Route;
use common\models\Seat;
use common\models\Booking;
use common\components\IpHelper;

class BookingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'bus', 'route', 'seat', 'review', 'pay', 'receipt', 'pdf-receipt', 'my-bookings', 'statistics', 'export'],
                'rules' => [
                    [
                        'actions' => ['index', 'bus', 'route', 'seat', 'review', 'pay', 'receipt', 'pdf-receipt', 'my-bookings', 'statistics', 'export'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            // Only allow regular users (not admin users) to book tickets
                            return $user && !($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager());
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'api-verify-ticket' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id === 'api-verify-ticket') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    // Step 1: Bus selection
    public function actionBus()
    {
        $buses = Bus::find()->all();
        return $this->render('bus', [
            'buses' => $buses,
        ]);
    }

    // Step 2: Route selection
    public function actionRoute($bus_id)
    {
        $bus = Bus::findOne($bus_id);
        if (!$bus) throw new NotFoundHttpException('Bus not found.');
        $routes = Route::find()->all();
        return $this->render('route', [
            'bus' => $bus,
            'routes' => $routes,
        ]);
    }

    // Step 3: Seat selection
    public function actionSeat($bus_id, $route_id)
    {
        $bus = Bus::findOne($bus_id);
        $route = Route::findOne($route_id);
        if (!$bus || !$route) throw new NotFoundHttpException('Invalid bus or route.');
        
        // Check if route is available for booking
        if ($route->status === 'in_progress') {
            Yii::$app->session->setFlash('error', 'This bus is currently on the route and unavailable for booking. Please select another bus or route.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        if ($route->status === 'completed') {
            Yii::$app->session->setFlash('error', 'This route has been completed and is no longer available for booking.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        if ($route->status === 'cancelled') {
            Yii::$app->session->setFlash('error', 'This route has been cancelled and is no longer available for booking.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        // Get all seats for this bus
        $seats = Seat::find()->where(['bus_id' => $bus_id])->orderBy(['seat_number' => SORT_ASC])->all();
        
        // Get booked seats for this route
        $bookedSeats = Booking::find()
            ->select(['seat.seat_number'])
            ->joinWith(['seat'])
            ->where(['booking.bus_id' => $bus_id, 'booking.route_id' => $route_id])
            ->column();
        
        return $this->render('seat', [
            'bus' => $bus,
            'route' => $route,
            'seats' => $seats,
            'bookedSeats' => $bookedSeats,
        ]);
    }

    // Step 5: Review booking details
    public function actionReview($bus_id, $route_id, $seat_id)
    {
        $bus = Bus::findOne($bus_id);
        $route = Route::findOne($route_id);
        $seat = Seat::findOne($seat_id);
        if (!$bus || !$route || !$seat) throw new NotFoundHttpException('Invalid booking details.');
        
        // Check if route is available for booking
        if ($route->status === 'in_progress') {
            Yii::$app->session->setFlash('error', 'This bus is currently on the route and unavailable for booking. Please select another bus or route.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        if ($route->status === 'completed') {
            Yii::$app->session->setFlash('error', 'This route has been completed and is no longer available for booking.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        if ($route->status === 'cancelled') {
            Yii::$app->session->setFlash('error', 'This route has been cancelled and is no longer available for booking.');
            return $this->redirect(['route', 'bus_id' => $bus_id]);
        }
        
        // Check if seat is already booked
        $existingBooking = Booking::find()->where(['seat_id' => $seat_id, 'bus_id' => $bus_id, 'route_id' => $route_id])->one();
        if ($existingBooking) {
            Yii::$app->session->setFlash('error', 'This seat has already been booked. Please select another seat.');
            return $this->redirect(['seat', 'bus_id' => $bus_id, 'route_id' => $route_id]);
        }
        
        // Calculate final price based on bus class
        $basePrice = $route->price;
        $finalPrice = $basePrice;
        
        return $this->render('review', [
            'bus' => $bus,
            'route' => $route,
            'seat' => $seat,
            'basePrice' => $basePrice,
            'finalPrice' => $finalPrice,
        ]);
    }

    // Step 6: Payment method selection
    public function actionPayment($bus_id, $route_id, $seat_id)
    {
        $bus = Bus::findOne($bus_id);
        $route = Route::findOne($route_id);
        $seat = Seat::findOne($seat_id);
        if (!$bus || !$route || !$seat) throw new NotFoundHttpException('Invalid booking details.');
        
        // Check if seat is already booked
        $existingBooking = Booking::find()->where(['seat_id' => $seat_id, 'bus_id' => $bus_id, 'route_id' => $route_id])->one();
        if ($existingBooking) {
            Yii::$app->session->setFlash('error', 'This seat has already been booked. Please select another seat.');
            return $this->redirect(['seat', 'bus_id' => $bus_id, 'route_id' => $route_id]);
        }
        
        // Calculate final price based on bus class
        $basePrice = $route->price;
        $finalPrice = $basePrice;
        
        // Define Tanzanian payment methods
        $paymentMethods = [
            'banks' => [
                'crdb' => [
                    'name' => 'CRDB Bank',
                    'icon' => 'bi-bank',
                    'description' => 'Pay via CRDB Bank',
                    'color' => 'primary'
                ],
                'nmb' => [
                    'name' => 'NMB Bank',
                    'icon' => 'bi-bank',
                    'description' => 'Pay via NMB Bank',
                    'color' => 'success'
                ]
            ],
            'mobile_money' => [
                'mpesa' => [
                    'name' => 'M-Pesa',
                    'icon' => 'bi-phone',
                    'description' => 'Pay via M-Pesa',
                    'color' => 'success'
                ],
                'airtel' => [
                    'name' => 'Airtel Money',
                    'icon' => 'bi-phone',
                    'description' => 'Pay via Airtel Money',
                    'color' => 'danger'
                ],
                'tigo' => [
                    'name' => 'Tigo Pesa',
                    'icon' => 'bi-phone',
                    'description' => 'Pay via Tigo Pesa',
                    'color' => 'warning'
                ],
                'tyas' => [
                    'name' => 'TYAS',
                    'icon' => 'bi-phone',
                    'description' => 'Pay via TYAS',
                    'color' => 'info'
                ]
            ]
        ];
        
        return $this->render('payment', [
            'bus' => $bus,
            'route' => $route,
            'seat' => $seat,
            'basePrice' => $basePrice,
            'finalPrice' => $finalPrice,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    // Step 6: Payment processing and booking creation
    public function actionPay($bus_id, $route_id, $seat_id, $payment_method)
    {
        $bus = Bus::findOne($bus_id);
        $route = Route::findOne($route_id);
        $seat = Seat::findOne($seat_id);
        if (!$bus || !$route || !$seat) throw new NotFoundHttpException('Invalid booking details.');
        
        // Check if seat is already booked
        $existingBooking = Booking::find()->where(['seat_id' => $seat_id, 'bus_id' => $bus_id, 'route_id' => $route_id])->one();
        if ($existingBooking) {
            Yii::$app->session->setFlash('error', 'This seat has already been booked. Please select another seat.');
            return $this->redirect(['seat', 'bus_id' => $bus_id, 'route_id' => $route_id]);
        }
        
        // Validate payment method
        $validMethods = ['crdb', 'nmb', 'mpesa', 'airtel', 'tigo', 'tyas'];
        if (!in_array($payment_method, $validMethods)) {
            Yii::$app->session->setFlash('error', 'Invalid payment method selected.');
            return $this->redirect(['payment', 'bus_id' => $bus_id, 'route_id' => $route_id, 'seat_id' => $seat_id]);
        }
        
        // Get payment method display name
        $paymentNames = [
            'crdb' => 'CRDB Bank',
            'nmb' => 'NMB Bank',
            'mpesa' => 'M-Pesa',
            'airtel' => 'Airtel Money',
            'tigo' => 'Tigo Pesa',
            'tyas' => 'TYAS'
        ];
        
        $selectedMethod = $paymentNames[$payment_method];
        
        // Create booking
        $booking = new Booking();
        $booking->user_id = Yii::$app->user->id;
        $booking->bus_id = $bus_id;
        $booking->route_id = $route_id;
        $booking->seat_id = $seat_id;
        $booking->payment_method = $selectedMethod;
        $booking->payment_status = 'completed';
        $booking->payment_info = "Paid via $selectedMethod (Development Mode)";
        $booking->status = 'confirmed';
        $booking->created_at = $booking->updated_at = time();
        
        // Generate unique QR code string
        $qrData = json_encode([
            'booking_id' => time() . '_' . Yii::$app->user->id,
            'user_id' => Yii::$app->user->id,
            'bus_id' => $bus_id,
            'route_id' => $route_id,
            'seat_id' => $seat_id,
            'timestamp' => time(),
        ]);
        $booking->qr_code = $qrData;
        
        if ($booking->save()) {
            // Mark seat as booked
            $seat->status = 'booked';
            $seat->save(false);
            
            Yii::$app->session->setFlash('success', "Payment successful via $selectedMethod! Your ticket has been booked.");
            return $this->redirect(['receipt', 'id' => $booking->id]);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to create booking. Please try again.');
            return $this->redirect(['payment', 'bus_id' => $bus_id, 'route_id' => $route_id, 'seat_id' => $seat_id]);
        }
    }

    // Step 6: Show receipt with QR code
    public function actionReceipt($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking || $booking->user_id != Yii::$app->user->id) throw new NotFoundHttpException('Receipt not found.');
        
        // Generate QR code with dynamic local IP address for mobile access
        $receiptUrl = IpHelper::getServerUrl() . "/booking/mobile-verify?id=" . $booking->id;
        
        $builder = new \Endroid\QrCode\Builder\Builder();
        $result = $builder->build(
            data: $receiptUrl,
            size: 200,
            margin: 10
        );
        
        // Convert to base64 for display
        $qrImageData = base64_encode($result->getString());
        
        return $this->render('receipt', [
            'booking' => $booking,
            'qrImageData' => $qrImageData,
            'receiptUrl' => $receiptUrl,
        ]);
    }

    // Mobile-optimized receipt view (for QR code scanning)
    public function actionMobileReceipt($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) throw new NotFoundHttpException('Receipt not found.');
        
        $print = Yii::$app->request->get('print', false);
        
        // Generate QR code with dynamic local IP address
        $receiptUrl = IpHelper::getServerUrl() . "/booking/mobile-verify?id=" . $booking->id;
        
        $builder = new \Endroid\QrCode\Builder\Builder();
        $result = $builder->build(
            data: $receiptUrl,
            size: 150,
            margin: 5
        );
        
        $qrImageData = base64_encode($result->getString());
        
        return $this->render('mobile_receipt', [
            'booking' => $booking,
            'qrImageData' => $qrImageData,
            'receiptUrl' => $receiptUrl,
            'print' => $print,
        ]);
    }

    // PDF Receipt generation
    public function actionPdfReceipt($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking || $booking->user_id != Yii::$app->user->id) throw new NotFoundHttpException('Receipt not found.');
        
        // Generate QR code for PDF using dynamic local IP address
        $receiptUrl = IpHelper::getServerUrl() . "/booking/mobile-verify?id=" . $booking->id;
        
        $builder = new \Endroid\QrCode\Builder\Builder();
        $result = $builder->build(
            data: $receiptUrl,
            size: 150,
            margin: 5
        );
        $qrImageData = base64_encode($result->getString());
        
        // Render receipt HTML
        $content = $this->renderPartial('receipt_pdf', [
            'booking' => $booking,
            'qrImageData' => $qrImageData,
        ]);
        
        // Generate PDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        
        $mpdf->WriteHTML($content);
        return Yii::$app->response->sendContentAsFile(
            $mpdf->Output('', 'S'),
            'Receipt_Booking_' . $booking->id . '.pdf',
            ['mimeType' => 'application/pdf']
        );
    }

    // My Bookings - User's booking history
    public function actionMyBookings()
    {
        $query = Booking::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->with(['bus', 'route', 'seat']);
        
        // Search functionality
        $search = Yii::$app->request->get('search');
        $status = Yii::$app->request->get('status');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        
        if ($search) {
            $query->andWhere(['or',
                ['like', 'bus.type', $search],
                ['like', 'route.origin', $search],
                ['like', 'route.destination', $search],
                ['like', 'seat.seat_number', $search],
            ]);
        }
        
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', strtotime($dateFrom)]);
        }
        
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', strtotime($dateTo . ' 23:59:59')]);
        }
        
        $bookings = $query->orderBy(['created_at' => SORT_DESC])->all();
            
        return $this->render('my-bookings', [
            'bookings' => $bookings,
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    // Cancel booking
    public function actionCancelBooking($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking || $booking->user_id != Yii::$app->user->id) throw new NotFoundHttpException('Booking not found.');
        
        if ($booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->updated_at = time();
            
            // Free up the seat
            $seat = Seat::findOne($booking->seat_id);
            if ($seat) {
                $seat->status = 'available';
                $seat->save(false);
            }
            
            if ($booking->save()) {
                Yii::$app->session->setFlash('success', 'Booking cancelled successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to cancel booking.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'This booking cannot be cancelled.');
        }
        
        return $this->redirect(['my-bookings']);
    }

    // Booking statistics and analytics
    public function actionStatistics()
    {
        $userId = Yii::$app->user->id;
        
        // Total bookings
        $totalBookings = Booking::find()->where(['user_id' => $userId])->count();
        
        // Confirmed bookings
        $confirmedBookings = Booking::find()->where(['user_id' => $userId, 'status' => 'confirmed'])->count();
        
        // Cancelled bookings
        $cancelledBookings = Booking::find()->where(['user_id' => $userId, 'status' => 'cancelled'])->count();
        
        // Total spent
        $totalSpent = Booking::find()
            ->where(['user_id' => $userId, 'status' => 'confirmed'])
            ->joinWith(['route'])
            ->sum('route.price');
        
        // Most frequent routes
        $frequentRoutes = Booking::find()
            ->select(['route_id', 'COUNT(*) as count'])
            ->where(['user_id' => $userId])
            ->joinWith(['route'])
            ->groupBy(['route_id'])
            ->orderBy(['count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();
        
        // Monthly booking trend
        $monthlyBookings = Booking::find()
            ->select(['DATE(FROM_UNIXTIME(created_at)) as month', 'COUNT(*) as count'])
            ->where(['user_id' => $userId])
            ->groupBy(['month'])
            ->orderBy(['month' => SORT_DESC])
            ->limit(12)
            ->asArray()
            ->all();
        
        // Recent bookings
        $recentBookings = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['bus', 'route', 'seat'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();
        
        return $this->render('statistics', [
            'totalBookings' => $totalBookings,
            'confirmedBookings' => $confirmedBookings,
            'cancelledBookings' => $cancelledBookings,
            'totalSpent' => $totalSpent ?: 0,
            'frequentRoutes' => $frequentRoutes,
            'monthlyBookings' => $monthlyBookings,
            'recentBookings' => $recentBookings,
        ]);
    }

    // Export booking history to CSV
    public function actionExport()
    {
        $userId = Yii::$app->user->id;
        
        // Get all user bookings
        $bookings = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['bus', 'route', 'seat'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        
        // Set response headers for CSV download
        $filename = 'my_bookings_' . date('Y-m-d_H-i-s') . '.csv';
        Yii::$app->response->setDownloadHeaders($filename, 'text/csv');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Write CSV header
        fputcsv($output, [
            'Booking ID',
            'Bus Type',
            'Plate Number',
            'Route',
            'Price (TZS)',
            'Seat Number',
            'Status',
            'Payment Method',
            'Payment Status',
            'Booking Date',
            'Last Updated'
        ]);
        
        // Write booking data
        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking->id,
                $booking->bus->type,
                $booking->bus->plate_number,
                $booking->route->origin . ' → ' . $booking->route->destination,
                number_format($booking->route->price),
                $booking->seat->seat_number,
                ucfirst($booking->status),
                $booking->payment_method ?: 'N/A',
                ucfirst($booking->payment_status ?: 'N/A'),
                date('Y-m-d H:i:s', $booking->created_at),
                date('Y-m-d H:i:s', $booking->updated_at)
            ]);
        }
        
        fclose($output);
        return Yii::$app->end();
    }

    // Booking notifications
    public function actionNotifications()
    {
        $userId = Yii::$app->user->id;
        
        // Get recent bookings with notifications
        $recentBookings = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['bus', 'route', 'seat'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();
        
        // Get upcoming trips (bookings in the next 7 days)
        $upcomingTrips = Booking::find()
            ->where(['user_id' => $userId, 'status' => 'confirmed'])
            ->andWhere(['>=', 'created_at', time() - (7 * 24 * 60 * 60)]) // Last 7 days
            ->with(['bus', 'route', 'seat'])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
        
        // Get cancelled bookings in the last 30 days
        $recentCancellations = Booking::find()
            ->where(['user_id' => $userId, 'status' => 'cancelled'])
            ->andWhere(['>=', 'updated_at', time() - (30 * 24 * 60 * 60)]) // Last 30 days
            ->with(['bus', 'route', 'seat'])
            ->orderBy(['updated_at' => SORT_DESC])
            ->all();
        
        return $this->render('notifications', [
            'recentBookings' => $recentBookings,
            'upcomingTrips' => $upcomingTrips,
            'recentCancellations' => $recentCancellations,
        ]);
    }

    // Test action to create a demo booking for QR code testing
    public function actionCreateTestBooking()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('error', 'Please login first to create a test booking.');
            return $this->redirect(['site/login']);
        }
        
        // Check if test booking already exists
        $existingTestBooking = Booking::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['like', 'payment_info', 'Test Booking'])
            ->one();
            
        if ($existingTestBooking) {
            Yii::$app->session->setFlash('info', 'Test booking already exists. Redirecting to receipt.');
            return $this->redirect(['receipt', 'id' => $existingTestBooking->id]);
        }
        
        // Get first available bus, route, and seat
        $bus = Bus::find()->one();
        $route = Route::find()->one();
        $seat = Seat::find()->where(['bus_id' => $bus->id, 'status' => 'available'])->one();
        
        if (!$bus || !$route || !$seat) {
            Yii::$app->session->setFlash('error', 'No buses, routes, or seats available for test booking.');
            return $this->redirect(['bus']);
        }
        
        // Create test booking
        $booking = new Booking();
        $booking->user_id = Yii::$app->user->id;
        $booking->bus_id = $bus->id;
        $booking->route_id = $route->id;
        $booking->seat_id = $seat->id;
        $booking->payment_method = 'M-Pesa (Test)';
        $booking->payment_status = 'completed';
        $booking->payment_info = 'Test Booking - QR Code Demo';
        $booking->status = 'confirmed';
        $booking->created_at = $booking->updated_at = time();
        
        // Generate unique QR code string
        $qrData = json_encode([
            'booking_id' => 'TEST_' . time() . '_' . Yii::$app->user->id,
            'user_id' => Yii::$app->user->id,
            'bus_id' => $bus->id,
            'route_id' => $route->id,
            'seat_id' => $seat->id,
            'timestamp' => time(),
        ]);
        $booking->qr_code = $qrData;
        
        if ($booking->save()) {
            // Mark seat as booked
            $seat->status = 'booked';
            $seat->save(false);
            
            Yii::$app->session->setFlash('success', 'Test booking created successfully! You can now test the QR code functionality.');
            return $this->redirect(['receipt', 'id' => $booking->id]);
        } else {
            Yii::$app->session->setFlash('error', 'Failed to create test booking. Please try again.');
            return $this->redirect(['bus']);
        }
    }

    // Ticket verification for boarding (scanned by staff/admin)
    public function actionVerifyTicket($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) {
            throw new NotFoundHttpException('Ticket not found.');
        }
        
        // Check if ticket is already used or expired
        if ($booking->isUsed()) {
            Yii::$app->session->setFlash('error', 'This ticket has already been used.');
            return $this->render('verify_ticket', [
                'booking' => $booking,
                'status' => 'used',
                'message' => 'This ticket has already been used for boarding.'
            ]);
        }
        
        if ($booking->isExpired()) {
            Yii::$app->session->setFlash('error', 'This ticket has expired.');
            return $this->render('verify_ticket', [
                'booking' => $booking,
                'status' => 'expired',
                'message' => 'This ticket has expired and cannot be used for boarding.'
            ]);
        }
        
        // If POST request, mark ticket as used
        if (Yii::$app->request->isPost) {
            $scannedByUserId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
            
            if ($booking->markAsUsed($scannedByUserId)) {
                Yii::$app->session->setFlash('success', 'Ticket verified successfully! Passenger can board.');
                return $this->render('verify_ticket', [
                    'booking' => $booking,
                    'status' => 'verified',
                    'message' => 'Ticket verified successfully! Passenger can board the bus.'
                ]);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to verify ticket. Please try again.');
            }
        }
        
        // Show verification form
        return $this->render('verify_ticket', [
            'booking' => $booking,
            'status' => 'active',
            'message' => 'Please verify passenger details and confirm boarding.'
        ]);
    }

    // Mobile ticket verification (for staff scanning)
    public function actionMobileVerify($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) {
            throw new NotFoundHttpException('Ticket not found.');
        }
        
        return $this->renderPartial('mobile_verify', [
            'booking' => $booking,
        ]);
    }

    // API endpoint for ticket verification (for mobile apps)
    public function actionApiVerifyTicket($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $booking = Booking::findOne($id);
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Ticket not found.',
                'status' => 'not_found'
            ];
        }
        
        // Check ticket status
        if ($booking->isUsed()) {
            return [
                'success' => false,
                'message' => 'This ticket has already been used.',
                'status' => 'used',
                'booking' => $this->getBookingData($booking)
            ];
        }
        
        if ($booking->isExpired()) {
            return [
                'success' => false,
                'message' => 'This ticket has expired.',
                'status' => 'expired',
                'booking' => $this->getBookingData($booking)
            ];
        }
        
        // Mark ticket as used
        $scannedByUserId = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
        
        if ($booking->markAsUsed($scannedByUserId)) {
            return [
                'success' => true,
                'message' => 'Ticket verified successfully! Passenger can board.',
                'status' => 'verified',
                'booking' => $this->getBookingData($booking)
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to verify ticket.',
                'status' => 'error'
            ];
        }
    }

    // Helper method to get booking data for API
    private function getBookingData($booking)
    {
        return [
            'id' => $booking->id,
            'passenger_name' => $booking->user->username,
            'passenger_email' => $booking->user->email,
            'bus_type' => $booking->bus->type,
            'bus_plate' => $booking->bus->plate_number,
            'route' => $booking->route->origin . ' → ' . $booking->route->destination,
            'seat_number' => $booking->seat->seat_number,
            'ticket_status' => $booking->ticket_status,
            'created_at' => date('Y-m-d H:i:s', $booking->created_at),
            'scanned_at' => $booking->scanned_at ? date('Y-m-d H:i:s', $booking->scanned_at) : null,
        ];
    }
} 