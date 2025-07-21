<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Route;
use backend\models\RouteSearch;

class RouteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete', 'exportCsv', 'exportExcel'],
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
        $searchModel = new RouteSearch();
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
        $model = new Route();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Route created successfully.');
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Route updated successfully.');
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Route deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete selected routes (superadmin only)
     */
    public function actionBulkDelete()
    {
        if (!Yii::$app->user->identity || !method_exists(Yii::$app->user->identity, 'isSuperAdmin') || !Yii::$app->user->identity->isSuperAdmin()) {
            throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
        }
        $ids = Yii::$app->request->post('selection', []);
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $route = \common\models\Route::findOne($id);
                if ($route) {
                    $route->delete();
                }
            }
            Yii::$app->session->setFlash('success', 'Selected routes have been deleted.');
        } else {
            Yii::$app->session->setFlash('warning', 'No routes selected for deletion.');
        }
        return $this->redirect(['index']);
    }

    public function actionExportCsv()
    {
        $searchModel = new \backend\models\RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $routes = $dataProvider->getModels();
        $filename = 'routes_' . date('Ymd_His') . '.csv';
        Yii::$app->response->setDownloadHeaders($filename, 'text/csv');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['ID', 'Origin', 'Destination', 'Price', 'Created At']);
        foreach ($routes as $route) {
            fputcsv($fp, [
                $route->id,
                $route->origin,
                $route->destination,
                $route->price,
                date('Y-m-d H:i', $route->created_at),
            ]);
        }
        fclose($fp);
        return Yii::$app->end();
    }

    public function actionExportExcel()
    {
        $searchModel = new \backend\models\RouteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $routes = $dataProvider->getModels();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->fromArray(['ID', 'Origin', 'Destination', 'Price (TZS)', 'Status', 'Created At'], null, 'A1');
        
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
        foreach ($routes as $route) {
            $sheet->fromArray([
                $route->id,
                $route->origin,
                $route->destination,
                number_format($route->price),
                $route->status,
                date('Y-m-d H:i', $route->created_at),
            ], null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'routes_' . date('Ymd_His') . '.xlsx';
        
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
        if (($model = Route::findOne($id)) !== null) {
            return $model;
        }
        throw new \yii\web\NotFoundHttpException('The requested route does not exist.');
    }
} 