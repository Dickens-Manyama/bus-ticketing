<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Booking;
use backend\models\BookingSearch;
use common\components\IpHelper;
use yii\web\NotFoundHttpException;

class BookingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'update', 'delete', 'exportCsv', 'exportExcel', 'verifyTicket', 'resetTicket'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'exportCsv', 'exportExcel'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager());
                        },
                    ],
                    [
                        'actions' => ['update', 'delete', 'verifyTicket', 'resetTicket'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isManager());
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
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Booking updated successfully.');
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Booking deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionExportCsv()
    {
        $searchModel = new \backend\models\BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $bookings = $dataProvider->getModels();
        $filename = 'bookings_' . date('Ymd_His') . '.csv';
        Yii::$app->response->setDownloadHeaders($filename, 'text/csv');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['ID', 'User', 'Bus', 'Route', 'Seat', 'Status', 'Payment Method', 'Payment Status', 'Ticket Status', 'Scanned At', 'Scanned By', 'Payment Info', 'Created At']);
        foreach ($bookings as $booking) {
            // Determine scanned by information
            $scannedBy = 'Not Scanned';
            if ($booking->ticket_status === 'used' && $booking->scanned_at) {
                if ($booking->scannedBy) {
                    $scannedBy = $booking->scannedBy->username . ' (' . $booking->scannedBy->role . ')';
                } else {
                    $scannedBy = 'Scanned by Phone (Mobile QR)';
                }
            }
            
            fputcsv($fp, [
                $booking->id,
                $booking->user->username,
                $booking->bus->type . ' (' . $booking->bus->plate_number . ')',
                $booking->route->origin . ' → ' . $booking->route->destination,
                $booking->seat->seat_number,
                $booking->status,
                $booking->payment_method,
                $booking->payment_status,
                $booking->ticket_status,
                $booking->scanned_at ? date('Y-m-d H:i', $booking->scanned_at) : 'Not Scanned',
                $scannedBy,
                $booking->payment_info,
                date('Y-m-d H:i', $booking->created_at),
            ]);
        }
        fclose($fp);
        return Yii::$app->end();
    }

    public function actionExportExcel()
    {
        $searchModel = new \backend\models\BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $bookings = $dataProvider->getModels();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->fromArray(['ID', 'User', 'Bus', 'Route', 'Seat', 'Status', 'Payment Method', 'Payment Status', 'Ticket Status', 'Scanned At', 'Scanned By', 'Payment Info', 'Created At'], null, 'A1');
        
        // Style the header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        // Add data rows
        $row = 2;
        foreach ($bookings as $booking) {
            // Determine scanned by information
            $scannedBy = 'Not Scanned';
            if ($booking->ticket_status === 'used' && $booking->scanned_at) {
                if ($booking->scannedBy) {
                    $scannedBy = $booking->scannedBy->username . ' (' . $booking->scannedBy->role . ')';
                } else {
                    $scannedBy = 'Scanned by Phone (Mobile QR)';
                }
            }
            
            $sheet->fromArray([
                $booking->id,
                $booking->user->username,
                $booking->bus->type . ' (' . $booking->bus->plate_number . ')',
                $booking->route->origin . ' → ' . $booking->route->destination,
                $booking->seat->seat_number,
                $booking->status,
                $booking->payment_method,
                $booking->payment_status,
                $booking->ticket_status,
                $booking->scanned_at ? date('Y-m-d H:i', $booking->scanned_at) : 'Not Scanned',
                $scannedBy,
                $booking->payment_info,
                date('Y-m-d H:i', $booking->created_at),
            ], null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'bookings_' . date('Ymd_His') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save to output
        $writer->save('php://output');
        exit;
    }

    // Ticket verification for admin/staff
    public function actionVerifyTicket($id)
    {
        $model = $this->findModel($id);
        
        // Check if ticket is already used or expired
        if ($model->isUsed()) {
            Yii::$app->session->setFlash('error', 'This ticket has already been used for boarding.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if ($model->isExpired()) {
            Yii::$app->session->setFlash('error', 'This ticket has expired and cannot be used for boarding.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Mark ticket as used
        if ($model->markAsUsed(Yii::$app->user->id)) {
            Yii::$app->session->setFlash('success', 'Ticket verified successfully! Passenger can board the bus.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to verify ticket. Please try again.');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    // Reset ticket status (for admin use only)
    public function actionResetTicket($id)
    {
        $model = $this->findModel($id);
        
        // Only superadmin and admin can reset tickets
        $user = Yii::$app->user->identity;
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdmin())) {
            Yii::$app->session->setFlash('error', 'You do not have permission to reset ticket status.');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        $model->ticket_status = 'active';
        $model->scanned_at = null;
        $model->scanned_by = null;
        $model->updated_at = time();
        
        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Ticket status reset to active successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to reset ticket status.');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionReceipt($id)
    {
        $booking = Booking::findOne($id);
        if (!$booking) throw new NotFoundHttpException('Receipt not found.');
        
        // Generate QR code with dynamic local IP address for mobile access
        $receiptUrl = IpHelper::getServerUrl() . "/booking/mobile-verify?id=" . $booking->id;
        
        $builder = new \Endroid\QrCode\Builder\Builder();
        $result = $builder->build(
            data: $receiptUrl,
            size: 200,
            margin: 10
        );
        
        $qrImageData = base64_encode($result->getString());
        
        return $this->render('receipt', [
            'booking' => $booking,
            'qrImageData' => $qrImageData,
            'receiptUrl' => $receiptUrl,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('The requested booking does not exist.');
    }
} 