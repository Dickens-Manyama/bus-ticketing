<?php

namespace backend\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $username;
    public $email;
    public $role;

    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['username', 'email'], 'string', 'max' => 255],
            ['email', 'email'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username can only contain letters, numbers, and underscores.'],
            ['role', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'role' => 'Role',
        ];
    }
} 