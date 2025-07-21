<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Bus;
use common\models\Booking;
use common\models\Route;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

class SeatMonitoringController extends Controller
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
                            return $user && ($user->isAdmin() || $user->isSuperAdmin());
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'start-journey' => ['post'],
                    'finish-journey' => ['post'],
                    'start-new-journey' => ['post'],
                    'get-seat-data' => ['get'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = Bus::find()
            ->with(['bookings.seat', 'route'])
            ->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionBusSeats($bus_id)
    {
        $bus = Bus::findOne($bus_id);
        if (!$bus) {
            throw new \yii\web\NotFoundHttpException('Bus not found.');
        }

        $bookings = Booking::find()
            ->where(['bus_id' => $bus_id])
            ->with(['user', 'route', 'seat'])
            ->all();

        $bookedSeats = [];
        foreach ($bookings as $booking) {
            if ($booking->seat) {
                $bookedSeats[] = $booking->seat->seat_number;
            }
        }

        return $this->render('bus-seats', [
            'bus' => $bus,
            'bookings' => $bookings,
            'bookedSeats' => $bookedSeats,
        ]);
    }

    public function actionGetSeatData($bus_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $bus = Bus::findOne($bus_id);
        if (!$bus) {
            return ['error' => 'Bus not found'];
        }

        $bookings = Booking::find()
            ->where(['bus_id' => $bus_id])
            ->with(['user', 'seat'])
            ->all();

        $bookedSeats = [];
        $bookingDetails = [];
        
        foreach ($bookings as $booking) {
            if ($booking->seat) {
                $bookedSeats[] = $booking->seat->seat_number;
                $bookingDetails[] = [
                    'id' => $booking->id,
                    'seat_number' => $booking->seat->seat_number,
                    'user' => $booking->user ? $booking->user->username : 'Unknown',
                    'status' => $booking->status,
                    'created_at' => date('Y-m-d H:i', $booking->created_at),
                ];
            }
        }

        return [
            'bus_id' => $bus_id,
            'total_seats' => $bus->seat_count,
            'booked_seats' => $bookedSeats,
            'available_seats' => $bus->seat_count - count($bookedSeats),
            'occupancy_rate' => $bus->seat_count > 0 ? round((count($bookedSeats) / $bus->seat_count) * 100, 1) : 0,
            'booking_details' => $bookingDetails,
            'bus_status' => $bus->status,
            'route_status' => $bus->route ? $bus->route->status : 'unknown',
            'last_updated' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Reset all bookings for a bus to make it ready for next journey
     */
    private function resetBusBookings($bus_id)
    {
        // Get all bookings for this bus
        $bookings = Booking::find()
            ->where(['bus_id' => $bus_id])
            ->all();

        foreach ($bookings as $booking) {
            // Mark booking as completed and ticket as used
            $booking->status = 'completed';
            $booking->ticket_status = 'used';
            $booking->updated_at = time();
            $booking->save(false);
        }

        // Reset seat status to available
        $seats = \common\models\Seat::find()
            ->where(['bus_id' => $bus_id])
            ->all();

        foreach ($seats as $seat) {
            $seat->status = 'available';
            $seat->updated_at = time();
            $seat->save(false);
        }
    }

    /**
     * Start a journey for a bus (when bus has bookings)
     */
    public function actionStartJourney($bus_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        Yii::info("StartJourney action called for bus_id: $bus_id", 'seat-monitoring');

        $bus = Bus::findOne($bus_id);
        if (!$bus) {
            return ['success' => false, 'message' => 'Bus not found'];
        }

        // Check if bus has any active bookings
        $activeBookings = Booking::find()
            ->where(['bus_id' => $bus_id, 'status' => ['pending', 'confirmed']])
            ->count();

        if ($activeBookings == 0) {
            return ['success' => false, 'message' => 'No bookings found. Please make bookings first.'];
        }

        return ['success' => true, 'message' => 'Journey started! Bus is now on the road.'];
    }

    /**
     * Finish current journey for a bus
     */
    public function actionFinishJourney($bus_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        Yii::info("FinishJourney action called for bus_id: $bus_id", 'seat-monitoring');

        $bus = Bus::findOne($bus_id);
        if (!$bus) {
            return ['success' => false, 'message' => 'Bus not found'];
        }

        // Check if bus has any active bookings
        $activeBookings = Booking::find()
            ->where(['bus_id' => $bus_id, 'status' => ['pending', 'confirmed']])
            ->count();

        if ($activeBookings == 0) {
            return ['success' => false, 'message' => 'No active journey to finish.'];
        }

        // Reset all bookings for this bus
        $this->resetBusBookings($bus_id);

        return ['success' => true, 'message' => 'Journey finished! All seats are now available.'];
    }

    /**
     * Start a new journey (reset bus and make it ready for new bookings)
     */
    public function actionStartNewJourney($bus_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        Yii::info("StartNewJourney action called for bus_id: $bus_id", 'seat-monitoring');

        $bus = Bus::findOne($bus_id);
        if (!$bus) {
            return ['success' => false, 'message' => 'Bus not found'];
        }

        // Reset any existing bookings to prepare for new journey
        $this->resetBusBookings($bus_id);

        return ['success' => true, 'message' => 'New journey started! Bus is ready for bookings.'];
    }

    public function actionRealTimeDashboard()
    {
        $buses = Bus::find()
            ->with(['route', 'bookings.seat'])
            ->all();

        $busData = [];
        foreach ($buses as $bus) {
            $bookedSeats = Booking::find()
                ->where(['bus_id' => $bus->id])
                ->count();

            // Get the most common route from bookings for this bus
            $bookings = $bus->bookings;
            $routeInfo = 'No Bookings';
            $routeStatus = 'unknown';
            
            if (!empty($bookings)) {
                // Group bookings by route_id and count them
                $routeCounts = [];
                foreach ($bookings as $booking) {
                    if ($booking->route) {
                        $routeId = $booking->route_id;
                        if (!isset($routeCounts[$routeId])) {
                            $routeCounts[$routeId] = [
                                'count' => 0,
                                'route' => $booking->route
                            ];
                        }
                        $routeCounts[$routeId]['count']++;
                    }
                }
                
                // Find the route with most bookings
                if (!empty($routeCounts)) {
                    $mostBookedRoute = null;
                    $maxCount = 0;
                    foreach ($routeCounts as $routeData) {
                        if ($routeData['count'] > $maxCount) {
                            $maxCount = $routeData['count'];
                            $mostBookedRoute = $routeData['route'];
                        }
                    }
                    
                    if ($mostBookedRoute) {
                        $routeInfo = $mostBookedRoute->origin . ' → ' . $mostBookedRoute->destination;
                        $routeStatus = $mostBookedRoute->status;
                    }
                }
            }

            $busData[] = [
                'id' => $bus->id,
                'plate_number' => $bus->plate_number,
                'class' => $bus->class,
                'total_seats' => $bus->seat_count,
                'booked_seats' => $bookedSeats,
                'available_seats' => $bus->seat_count - $bookedSeats,
                'occupancy_rate' => $bus->seat_count > 0 ? round(($bookedSeats / $bus->seat_count) * 100, 1) : 0,
                'status' => $bus->status,
                'route_status' => $routeStatus,
                'route_info' => $routeInfo,
            ];
        }

        return $this->render('real-time-dashboard', [
            'busData' => $busData,
        ]);
    }
} 