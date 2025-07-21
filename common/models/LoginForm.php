<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $role;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // role is required only for backend login
            ['role', 'required', 'when' => function($model) {
                return Yii::$app->id === 'app-backend';
            }, 'message' => 'Please select your role.'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['role', 'validateRole'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Validates the selected role against the user's actual role.
     */
    public function validateRole($attribute, $params)
    {
        // Only validate role if it's provided (for backend login)
        if ($this->role && !$this->hasErrors()) {
            $user = $this->getUser();
            $allowedRoles = ['superadmin', 'admin', 'manager', 'staff'];
            if (!$user || !in_array($this->role, $allowedRoles) || $user->role !== $this->role) {
                $this->addError($attribute, 'You are not allowed to log in with this role.');
            }
        }
        // For frontend login, no role validation is needed - user will be redirected based on their actual role
    }

    /**
     * Logs in a user using the provided username, password, and role.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                // Update last login timestamp
                $user->updateLastLogin();
                
                // Set remember me duration (30 days if checked, 0 if not)
                $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
                
                return Yii::$app->user->login($user, $duration);
            }
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
