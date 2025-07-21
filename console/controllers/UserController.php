<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;

class UserController extends Controller
{
    public function actionCheckStaff()
    {
        $staffUser = User::find()->where(['username' => 'staff'])->one();
        
        if ($staffUser) {
            echo "Staff user found:\n";
            echo "ID: " . $staffUser->id . "\n";
            echo "Username: " . $staffUser->username . "\n";
            echo "Email: " . $staffUser->email . "\n";
            echo "Role: " . $staffUser->role . "\n";
            echo "Status: " . $staffUser->status . "\n";
            echo "Created: " . date('Y-m-d H:i:s', $staffUser->created_at) . "\n";
        } else {
            echo "Staff user not found!\n";
        }
    }

    public function actionListAll()
    {
        $users = User::find()->all();
        
        echo "All users:\n";
        foreach ($users as $user) {
            echo sprintf(
                "ID: %d, Username: %s, Email: %s, Role: %s, Status: %d\n",
                $user->id,
                $user->username,
                $user->email,
                $user->role,
                $user->status
            );
        }
    }

    public function actionCreateStaff()
    {
        $staffUser = User::find()->where(['username' => 'staff'])->one();
        
        if ($staffUser) {
            echo "Staff user already exists!\n";
            return;
        }

        $user = new User();
        $user->username = 'staff';
        $user->email = 'staff@admin.com';
        $user->role = 'staff';
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword('Admin@123');
        $user->generateAuthKey();
        $user->created_at = $user->updated_at = time();

        if ($user->save()) {
            echo "Staff user created successfully!\n";
        } else {
            echo "Failed to create staff user:\n";
            print_r($user->errors);
        }
    }
} 