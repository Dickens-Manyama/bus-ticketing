<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\User;
use backend\models\ChangePasswordForm;
use backend\models\ProfileForm;

class ProfileController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'update', 'change-password', 'upload-picture', 'test'],
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'change-password', 'upload-picture', 'test'],
                        'allow' => true,
                        'roles' => ['@'],
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'upload-picture' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $changePasswordForm = new ChangePasswordForm();
        $profileForm = new ProfileForm();
        
        // Debug: Log the action
        Yii::info('Backend Profile index action called for user: ' . $user->username, 'profile');
        
        // Load current user data into profile form
        $profileForm->username = $user->username;
        $profileForm->email = $user->email;
        $profileForm->role = $user->role;
        
        return $this->render('index', [
            'user' => $user,
            'changePasswordForm' => $changePasswordForm,
            'profileForm' => $profileForm,
        ]);
    }

    public function actionUpdate()
    {
        $user = Yii::$app->user->identity;
        $profileForm = new ProfileForm();
        
        if ($profileForm->load(Yii::$app->request->post()) && $profileForm->validate()) {
            // Check if username is being changed and if it's already taken
            if ($profileForm->username !== $user->username) {
                $existingUser = User::findByUsername($profileForm->username);
                if ($existingUser && $existingUser->id !== $user->id) {
                    Yii::$app->session->setFlash('error', 'Username is already taken.');
                    return $this->redirect(['index']);
                }
            }
            
            // Check if email is being changed and if it's already taken
            if ($profileForm->email !== $user->email) {
                $existingUser = User::findOne(['email' => $profileForm->email]);
                if ($existingUser && $existingUser->id !== $user->id) {
                    Yii::$app->session->setFlash('error', 'Email is already taken.');
                    return $this->redirect(['index']);
                }
            }
            
            // Update user profile
            $user->username = $profileForm->username;
            $user->email = $profileForm->email;
            
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Profile updated successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to update profile.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Please correct the errors below.');
        }
        
        return $this->redirect(['index']);
    }

    public function actionChangePassword()
    {
        $user = Yii::$app->user->identity;
        $changePasswordForm = new ChangePasswordForm();
        
        if ($changePasswordForm->load(Yii::$app->request->post()) && $changePasswordForm->validate()) {
            // Verify current password
            if (!$user->validatePassword($changePasswordForm->currentPassword)) {
                Yii::$app->session->setFlash('error', 'Current password is incorrect.');
                return $this->redirect(['index']);
            }
            
            // Set new password
            $user->setPassword($changePasswordForm->newPassword);
            
            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Password changed successfully.');
            } else {
                Yii::$app->session->setFlash('error', 'Failed to change password.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Please correct the errors below.');
        }
        
        return $this->redirect(['index']);
    }

    public function actionUploadPicture()
    {
        $user = Yii::$app->user->identity;
        $file = UploadedFile::getInstanceByName('profile_picture');
        
        if ($file) {
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->type, $allowedTypes)) {
                Yii::$app->session->setFlash('error', 'Only JPG, PNG, and GIF files are allowed.');
                return $this->redirect(['index']);
            }
            
            // Validate file size (max 2MB)
            if ($file->size > 2 * 1024 * 1024) {
                Yii::$app->session->setFlash('error', 'File size must be less than 2MB.');
                return $this->redirect(['index']);
            }
            
            // Generate unique filename
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->extension;
            $uploadPath = Yii::getAlias('@backend/web/uploads/profiles/');
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $filePath = $uploadPath . $fileName;
            
            if ($file->saveAs($filePath)) {
                // Delete old profile picture if exists
                if ($user->profile_picture && file_exists(Yii::getAlias('@backend/web') . $user->profile_picture)) {
                    unlink(Yii::getAlias('@backend/web') . $user->profile_picture);
                }
                
                // Update user profile picture
                $user->profile_picture = '/uploads/profiles/' . $fileName;
                
                if ($user->save()) {
                    Yii::$app->session->setFlash('success', 'Profile picture updated successfully.');
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to update profile picture.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Failed to upload file.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'No file selected.');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Test action to verify profile functionality
     */
    public function actionTest()
    {
        $user = Yii::$app->user->identity;
        
        $data = [
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'profile_picture' => $user->profile_picture,
            'last_login' => $user->last_login ? date('Y-m-d H:i:s', $user->last_login) : 'Never',
            'created_at' => date('Y-m-d H:i:s', $user->created_at),
            'updated_at' => date('Y-m-d H:i:s', $user->updated_at),
            'profile_picture_exists' => $user->profile_picture ? file_exists(Yii::getAlias('@backend/web') . $user->profile_picture) : false,
            'uploads_dir_exists' => is_dir(Yii::getAlias('@backend/web/uploads/profiles')),
            'uploads_dir_writable' => is_writable(Yii::getAlias('@backend/web/uploads/profiles')),
        ];
        
        return $this->render('test', ['data' => $data]);
    }
} 