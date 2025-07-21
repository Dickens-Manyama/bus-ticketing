<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\behaviors\SecurityBehavior;
use common\components\SecurityMiddleware;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'test'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'debug'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->user->identity;
                            return $user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager());
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        Yii::$app->user->logout();
                        Yii::$app->session->setFlash('error', 'You must be an administrator to access the backend.');
                        return Yii::$app->response->redirect(['site/login']);
                    }
                },
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Check if user is an administrator
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        
        $user = Yii::$app->user->identity;
        if (!$user || !($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager())) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', 'You must be an administrator to access the backend.');
            return $this->redirect(['site/login']);
        }
        
        return $this->redirect(['dashboard/index']);
    }

    /**
     * Login action with enhanced security.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        // If user is logged in, check if they are an administrator
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            // Check if user is an administrator (admin, superadmin, manager, or staff)
            if ($user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager())) {
                return $this->redirect(['dashboard/index']);
            } else {
                // User is logged in but not an administrator, log them out
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'You must be an administrator to access the backend.');
            }
        }

        $this->layout = 'blank';

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
                // Check if the logged-in user is an administrator
                $user = Yii::$app->user->identity;
                if ($user && ($user->isAdmin() || $user->isSuperAdmin() || $user->isStaff() || $user->isManager())) {
                    // Clear failed login attempts on successful login
                    $securityMiddleware->clearFailedAttempts($username);
                    
                    // Update last login timestamp
                    $user->updateLastLogin();
                    
                    // Log successful login
                    $securityMiddleware->logSecurityEvent('successful_login', [
                        'username' => $username,
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'ip' => Yii::$app->request->userIP,
                    ]);
                    
                    return $this->redirect(['dashboard/index']);
                } else {
                    // User is not an administrator, log them out and show error
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', 'You must be an administrator to access the backend.');
                }
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
     * Debug action to test backend functionality
     */
    public function actionDebug()
    {
        $this->layout = 'blank';
        
        $data = [
            'app_id' => Yii::$app->id,
            'user_guest' => Yii::$app->user->isGuest,
            'user_identity' => Yii::$app->user->identity ? Yii::$app->user->identity->username : 'None',
            'user_role' => Yii::$app->user->identity ? Yii::$app->user->identity->role : 'None',
            'database_connected' => false,
            'current_url' => Yii::$app->request->url,
            'base_url' => Yii::$app->request->baseUrl,
            'home_url' => Yii::$app->homeUrl,
            'default_route' => Yii::$app->defaultRoute,
            'security_headers' => [
                'x_content_type_options' => Yii::$app->response->headers->get('X-Content-Type-Options'),
                'x_frame_options' => Yii::$app->response->headers->get('X-Frame-Options'),
                'x_xss_protection' => Yii::$app->response->headers->get('X-XSS-Protection'),
                'content_security_policy' => Yii::$app->response->headers->get('Content-Security-Policy'),
            ],
        ];
        
        try {
            $test = Yii::$app->db->createCommand('SELECT 1')->queryScalar();
            $data['database_connected'] = $test == 1;
        } catch (\Exception $e) {
            $data['database_error'] = $e->getMessage();
        }
        
        return $this->render('debug', ['data' => $data]);
    }

    /**
     * Test action to check if backend is accessible
     */
    public function actionTest()
    {
        $this->layout = 'blank';
        
        echo "<h1>Backend Test Page</h1>";
        echo "<p>If you can see this, the backend is accessible.</p>";
        echo "<p>Current URL: " . Yii::$app->request->url . "</p>";
        echo "<p>Base URL: " . Yii::$app->request->baseUrl . "</p>";
        echo "<p>Home URL: " . Yii::$app->homeUrl . "</p>";
        echo "<p>Default Route: " . Yii::$app->defaultRoute . "</p>";
        
        if (!Yii::$app->user->isGuest) {
            echo "<p>Logged in as: " . Yii::$app->user->identity->username . " (Role: " . Yii::$app->user->identity->role . ")</p>";
            echo "<p><a href='" . Yii::$app->urlManager->createUrl(['dashboard/index']) . "'>Go to Dashboard</a></p>";
        } else {
            echo "<p>Not logged in</p>";
            echo "<p><a href='" . Yii::$app->urlManager->createUrl(['site/login']) . "'>Login</a></p>";
        }
    }

    /**
     * Logout action with security logging.
     *
     * @return Response
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
        
        // Redirect to backend login page with success message
        Yii::$app->session->setFlash('success', 'You have been successfully logged out. Goodbye, ' . $username . '!');
        return $this->redirect(['site/login']);
    }
}
