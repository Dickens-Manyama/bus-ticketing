<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\Bus;
use common\models\Seat;
use backend\models\BusSearch;

class BusController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete', 'view', 'exportCsv', 'exportExcel'],
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
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isManager());
                        },
                    ],
                    'denyCallback' => function ($rule, $action) {
                        if (Yii::$app->user->isGuest) {
                            return Yii::$app->response->redirect(['site/login']);
                        } else {
                            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
                        }
                    },
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BusSearch();
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

    public function actionCreate()
    {
        $model = new Bus();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            if ($file) {
                $fileName = 'bus_' . time() . '.' . $file->extension;
                $filePath = Yii::getAlias('@backend/web/uploads/bus/') . $fileName;
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }
                $file->saveAs($filePath);
                $model->image = '/uploads/bus/' . $fileName;
            }
            // Set seat_count based on type
            if ($model->type === 'Luxury') {
                $model->seat_count = 30;
            } elseif ($model->type === 'Semi-Luxury') {
                $model->seat_count = 40;
            } elseif ($model->type === 'Middle Class') {
                $model->seat_count = 60;
            }
            $model->created_at = $model->updated_at = time();
            if ($model->save()) {
                // Auto-generate seats
                \common\models\Bus::generateSeatsForBus($model);
                Yii::$app->session->setFlash('success', 'Bus created successfully.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'image');
            if ($file) {
                $fileName = 'bus_' . time() . '.' . $file->extension;
                $filePath = Yii::getAlias('@backend/web/uploads/bus/') . $fileName;
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }
                $file->saveAs($filePath);
                $model->image = '/uploads/bus/' . $fileName;
            }
            $model->updated_at = time();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Bus updated successfully.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Bus deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete selected buses (routes)
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('selection', []);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $bus = \common\models\Bus::findOne($id);
                if ($bus) {
                    $bus->delete();
                }
            }
            Yii::$app->session->setFlash('success', 'Selected buses have been deleted.');
        } else {
            Yii::$app->session->setFlash('warning', 'No buses selected for deletion.');
        }
        return $this->redirect(['index']);
    }

    public function actionExportCsv()
    {
        $searchModel = new \backend\models\BusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $buses = $dataProvider->getModels();
        $filename = 'buses_' . date('Ymd_His') . '.csv';
        Yii::$app->response->setDownloadHeaders($filename, 'text/csv');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['ID', 'Type', 'Plate Number', 'Seat Count', 'Created At']);
        foreach ($buses as $bus) {
            fputcsv($fp, [
                $bus->id,
                $bus->type,
                $bus->plate_number,
                $bus->seat_count,
                date('Y-m-d H:i', $bus->created_at),
            ]);
        }
        fclose($fp);
        return Yii::$app->end();
    }

    public function actionExportExcel()
    {
        $searchModel = new \backend\models\BusSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $buses = $dataProvider->getModels();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->fromArray(['ID', 'Type', 'Plate Number', 'Seat Count', 'Status', 'Created At'], null, 'A1');
        
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
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        
        // Add data rows
        $row = 2;
        foreach ($buses as $bus) {
            $sheet->fromArray([
                $bus->id,
                $bus->type,
                $bus->plate_number,
                $bus->seat_count,
                $bus->status,
                date('Y-m-d H:i', $bus->created_at),
            ], null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'buses_' . date('Ymd_His') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save to output
        $writer->save('php://output');
        exit;
    }

    protected function findModel($id)
    {
        if (($model = Bus::findOne($id)) !== null) {
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('The requested bus does not exist.');
    }
} 