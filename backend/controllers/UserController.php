<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use common\models\User;
use backend\models\UserSearch;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete', 'bulk-status-update', 'exportCsv', 'exportExcel', 'staff-activity'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'exportCsv', 'exportExcel', 'staff-activity'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isManager());
                        },
                    ],
                    [
                        'actions' => ['create', 'update', 'delete', 'bulk-status-update'],
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
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        // Get status counts for summary
        $statusCounts = [
            'total' => User::find()->count(),
            'active' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
            'inactive' => User::find()->where(['status' => User::STATUS_INACTIVE])->count(),
            'deleted' => User::find()->where(['status' => User::STATUS_DELETED])->count(),
        ];
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statusCounts' => $statusCounts,
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
        $model = new User();
        $model->scenario = 'create';
        $user = Yii::$app->user->identity;
        // Only superadmin can assign roles
        if ($user->isManager()) {
            $model->role = 'staff';
        } elseif (!$user->isSuperAdmin()) {
            $model->scenario = 'noRoleChange';
        }
        if ($model->load(Yii::$app->request->post())) {
            // Prevent manager from creating non-staff
            if ($user->isManager() && $model->role !== 'staff') {
                Yii::$app->session->setFlash('error', 'Managers can only create staff accounts.');
                return $this->redirect(['index']);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User created successfully.');
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
        $user = Yii::$app->user->identity;
        // Prevent manager from editing non-staff
        if ($user->isManager() && $model->role !== 'staff') {
            Yii::$app->session->setFlash('error', 'Managers can only edit staff accounts.');
            return $this->redirect(['index']);
        }
        // Prevent admin from editing superadmin
        if (!$user->isSuperAdmin() && $model->role === 'superadmin') {
            Yii::$app->session->setFlash('error', 'You are not allowed to edit a Super Admin.');
            return $this->redirect(['index']);
        }
        // Only superadmin can assign roles
        if ($user->isManager()) {
            $model->role = 'staff';
        } elseif (!$user->isSuperAdmin()) {
            $model->scenario = 'noRoleChange';
        }
        if ($model->load(Yii::$app->request->post())) {
            // Prevent manager from changing role to non-staff
            if ($user->isManager() && $model->role !== 'staff') {
                Yii::$app->session->setFlash('error', 'Managers can only assign the staff role.');
                return $this->redirect(['index']);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User updated successfully.');
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;
        // Prevent manager from deleting non-staff
        if ($user->isManager() && $model->role !== 'staff') {
            Yii::$app->session->setFlash('error', 'Managers can only delete staff accounts.');
            return $this->redirect(['index']);
        }
        // Prevent admin from deleting superadmin
        if (!$user->isSuperAdmin() && $model->role === 'superadmin') {
            Yii::$app->session->setFlash('error', 'You are not allowed to delete a Super Admin.');
            return $this->redirect(['index']);
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'User deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionBulkStatusUpdate()
    {
        if (Yii::$app->request->isPost) {
            $userIds = Yii::$app->request->post('userIds', []);
            $status = Yii::$app->request->post('status');
            
            if (!empty($userIds) && in_array($status, [User::STATUS_ACTIVE, User::STATUS_INACTIVE, User::STATUS_DELETED])) {
                $updated = User::updateAll(['status' => $status], ['id' => $userIds]);
                Yii::$app->session->setFlash('success', "Status updated for {$updated} user(s).");
            } else {
                Yii::$app->session->setFlash('error', 'Invalid request.');
            }
        }
        
        return $this->redirect(['index']);
    }

    public function actionExportCsv()
    {
        $searchModel = new \backend\models\UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $users = $dataProvider->getModels();
        $filename = 'users_' . date('Ymd_His') . '.csv';
        Yii::$app->response->setDownloadHeaders($filename, 'text/csv');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['ID', 'Username', 'Email', 'Status', 'Role', 'Profile Picture', 'Created At', 'Last Updated']);
        foreach ($users as $user) {
            fputcsv($fp, [
                $user->id,
                $user->username,
                $user->email,
                $this->getStatusLabel($user->status),
                ucfirst($user->role),
                $user->profile_picture ?: 'No Picture',
                date('Y-m-d H:i:s', $user->created_at),
                date('Y-m-d H:i:s', $user->updated_at),
            ]);
        }
        fclose($fp);
        return Yii::$app->end();
    }

    public function actionExportExcel()
    {
        $searchModel = new \backend\models\UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $users = $dataProvider->getModels();
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->fromArray(['ID', 'Username', 'Email', 'Status', 'Role', 'Profile Picture', 'Created At', 'Last Updated'], null, 'A1');
        
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
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        
        // Add data rows
        $row = 2;
        foreach ($users as $user) {
            $sheet->fromArray([
                $user->id,
                $user->username,
                $user->email,
                $this->getStatusLabel($user->status),
                ucfirst($user->role),
                $user->profile_picture ?: 'No Picture',
                date('Y-m-d H:i:s', $user->created_at),
                date('Y-m-d H:i:s', $user->updated_at),
            ], null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Create Excel file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'users_' . date('Ymd_His') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save to output
        $writer->save('php://output');
        exit;
    }

    public function actionStaffActivity()
    {
        $user = Yii::$app->user->identity;
        if (!$user->isManager()) {
            Yii::$app->session->setFlash('error', 'Only managers can view staff activity.');
            return $this->redirect(['index']);
        }
        $staffUsers = User::find()->where(['role' => 'staff'])->all();
        $staffIds = array_map(function($u) { return $u->id; }, $staffUsers);
        $bookings = \common\models\Booking::find()->where(['user_id' => $staffIds])->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('staff-activity', [
            'bookings' => $bookings,
            'staffUsers' => $staffUsers,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested user does not exist.');
    }

    protected function getStatusLabel($status)
    {
        switch ($status) {
            case User::STATUS_ACTIVE:
                return 'Active';
            case User::STATUS_INACTIVE:
                return 'Inactive';
            case User::STATUS_DELETED:
                return 'Deleted';
            default:
                return 'Unknown';
        }
    }
} 