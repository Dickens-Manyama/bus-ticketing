<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use common\models\User;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'update'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = Yii::$app->user->identity;
        
        // Debug: Log the action
        Yii::info('Profile index action called for user: ' . $model->username, 'profile');
        
        // Show notice for admin users but allow them to view their profile
        if ($model && ($model->isAdmin() || $model->isSuperAdmin() || $model->isStaff() || $model->isManager())) {
            Yii::$app->session->setFlash('info', 'You are logged in as an administrator. You can also access the backend for full admin features.');
        }
        
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionView()
    {
        $model = Yii::$app->user->identity;
        
        // Show notice for admin users but allow them to view their profile
        if ($model && ($model->isAdmin() || $model->isSuperAdmin() || $model->isStaff() || $model->isManager())) {
            Yii::$app->session->setFlash('info', 'You are logged in as an administrator. You can also access the backend for full admin features.');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionUpdate()
    {
        $model = Yii::$app->user->identity;
        
        // Show notice for admin users but allow them to update their profile
        if ($model && ($model->isAdmin() || $model->isSuperAdmin() || $model->isStaff() || $model->isManager())) {
            Yii::$app->session->setFlash('info', 'You are logged in as an administrator. You can also access the backend at http://192.168.100.76:8080 for full admin features.');
        }
        
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'profile_picture');
            if ($file) {
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file->type, $allowedTypes)) {
                    Yii::$app->session->setFlash('error', 'Only JPG, PNG, and GIF files are allowed.');
                    return $this->redirect(['view']);
                }
                
                // Validate file size (max 2MB)
                if ($file->size > 2 * 1024 * 1024) {
                    Yii::$app->session->setFlash('error', 'File size must be less than 2MB.');
                    return $this->redirect(['view']);
                }
                
                $fileName = 'profile_' . $model->id . '_' . time() . '.' . $file->extension;
                $filePath = Yii::getAlias('@frontend/web/uploads/profile/') . $fileName;
                
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0777, true);
                }
                
                if ($file->saveAs($filePath)) {
                    // Delete old profile picture if exists
                    if ($model->profile_picture && file_exists(Yii::getAlias('@frontend/web') . $model->profile_picture)) {
                        unlink(Yii::getAlias('@frontend/web') . $model->profile_picture);
                    }
                    $model->profile_picture = '/uploads/profile/' . $fileName;
                }
            }
            
            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Profile updated successfully.');
                return $this->redirect(['view']);
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update profile.');
            }
        }
        
        return $this->render('update', [
            'model' => $model,
        ]);
    }
} 