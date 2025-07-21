<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['currentPassword', 'newPassword', 'confirmPassword'], 'required'],
            [['currentPassword', 'newPassword', 'confirmPassword'], 'string', 'min' => 6],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Passwords do not match.'],
            ['newPassword', 'match', 'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', 
             'message' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'currentPassword' => 'Current Password',
            'newPassword' => 'New Password',
            'confirmPassword' => 'Confirm New Password',
        ];
    }
} 