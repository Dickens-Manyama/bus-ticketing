<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\behaviors\SecurityBehavior;
use common\components\SecurityMiddleware;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'security' => [
                'class' => SecurityBehavior::class,
                'checkBruteForce' => true,
                'logSecurityEvents' => true,
                'skipSecurityActions' => ['error', 'captcha', 'test'],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // Always show the frontend home page
        // Administrators can access the backend through the admin notice or direct URL
        return $this->render('index');
    }

    /**
     * Logs in a user with enhanced security.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            // If user is already logged in, redirect based on role
            $user = Yii::$app->user->identity;
            return $this->redirectBasedOnRole($user);
        }

        $model = new LoginForm();
        $securityMiddleware = new SecurityMiddleware();
        
        if ($model->load(Yii::$app->request->post())) {
            $username = $model->username;
            
            // Check for brute force attempts
            if (!$securityMiddleware->checkBruteForceAttempts($username)) {
                $securityMiddleware->logSecurityEvent('brute_force_blocked', ['username' => $username]);
                Yii::$app->session->setFlash('error', 'Account temporarily locked due to multiple failed login attempts. Please try again later.');
                $model->password = '';
                return $this->render('login', ['model' => $model]);
            }
            
            // Attempt login
            if ($model->login()) {
                $user = Yii::$app->user->identity;
                
                // Check if user is an administrator
                if ($user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager())) {
                    // Administrators should use backend login
                    Yii::$app->user->logout();
                    $securityMiddleware->logSecurityEvent('admin_frontend_login_attempt', [
                        'username' => $username,
                        'ip' => Yii::$app->request->userIP,
                    ]);
                    Yii::$app->session->setFlash('error', 'Administrators must log in through the backend system. Please use the admin login page.');
                    return $this->redirect(['site/login']);
                }
                
                // Clear failed login attempts on successful login
                $securityMiddleware->clearFailedAttempts($username);
                
                // Update last login timestamp for regular users
                if ($user) {
                    $user->updateLastLogin();
                }
                
                // Log successful login
                $securityMiddleware->logSecurityEvent('successful_login', [
                    'username' => $username,
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'ip' => Yii::$app->request->userIP,
                ]);
                
                // Redirect based on user role
                return $this->redirectBasedOnRole($user);
            } else {
                // Login failed - record failed attempt
                $securityMiddleware->recordFailedAttempt($username);
                
                // Log failed login attempt
                $securityMiddleware->logSecurityEvent('failed_login', [
                    'username' => $username,
                    'ip' => Yii::$app->request->userIP,
                ]);
                
                Yii::$app->session->setFlash('error', 'Invalid username or password.');
            }
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Redirects user based on their role
     * 
     * @param \common\models\User $user
     * @return \yii\web\Response
     */
    private function redirectBasedOnRole($user)
    {
        if (!$user) {
            return $this->goHome();
        }

        // Debug: Log user information
        Yii::info("Frontend login - User: {$user->username}, Role: {$user->role}, ID: {$user->id}", 'login');

        // Check if user is an administrator
        if ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager()) {
            // Administrators should use backend
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', 'Administrators must log in through the backend system. Please use the admin login page.');
            return $this->redirect(['site/login']);
        }

        // Regular users stay on frontend
        Yii::info("Keeping user {$user->username} ({$user->role}) on frontend", 'login');
        Yii::$app->session->setFlash('success', 'Welcome back, ' . $user->username . '!');
        return $this->goHome();
    }

    /**
     * Logs out the current user with security logging.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        $username = Yii::$app->user->identity ? Yii::$app->user->identity->username : 'User';
        $userRole = Yii::$app->user->identity ? Yii::$app->user->identity->role : '';
        
        // Log logout event
        $securityMiddleware = new SecurityMiddleware();
        $securityMiddleware->logSecurityEvent('logout', [
            'username' => $username,
            'role' => $userRole,
            'ip' => Yii::$app->request->userIP,
        ]);
        
        Yii::$app->user->logout();
        
        // Clear any admin-related session data
        Yii::$app->session->remove('admin_redirect');
        
        $message = 'You have been successfully logged out. Goodbye, ' . $username . '!';
        if ($userRole && ($userRole === 'superadmin' || $userRole === 'admin' || $userRole === 'manager' || $userRole === 'staff')) {
            $message .= ' You can now access the customer booking system.';
        }
        
        Yii::$app->session->setFlash('success', $message);
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Switch language action
     * @param string $lang
     * @return \yii\web\Response
     */
    public function actionSwitchLanguage(
        $lang = 'en'
    ) {
        Yii::$app->session->set('language', $lang);
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }

    /**
     * Test action to check if frontend is accessible
     */
    public function actionTest()
    {
        $this->layout = 'blank';
        
        echo "<h1>Frontend Test Page</h1>";
        echo "<p>If you can see this, the frontend is accessible.</p>";
        echo "<p>Current URL: " . Yii::$app->request->url . "</p>";
        echo "<p>Base URL: " . Yii::$app->request->baseUrl . "</p>";
        echo "<p>Home URL: " . Yii::$app->homeUrl . "</p>";
        
        if (!Yii::$app->user->isGuest) {
            echo "<p>Logged in as: " . Yii::$app->user->identity->username . " (Role: " . Yii::$app->user->identity->role . ")</p>";
            echo "<p><a href='" . Yii::$app->urlManager->createUrl(['site/index']) . "'>Go to Home</a></p>";
        } else {
            echo "<p>Not logged in</p>";
            echo "<p><a href='" . Yii::$app->urlManager->createUrl(['site/login']) . "'>Login</a></p>";
        }
    }
}
