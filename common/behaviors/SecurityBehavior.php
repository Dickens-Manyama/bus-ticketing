<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use yii\web\BadRequestHttpException;
use common\components\SecurityMiddleware;

/**
 * Security Behavior
 * Applies security measures to controllers
 */
class SecurityBehavior extends Behavior
{
    /**
     * @var array List of actions that should be protected
     */
    public $protectedActions = [];
    
    /**
     * @var array List of actions that should skip security checks
     */
    public $skipSecurityActions = ['error', 'captcha'];
    
    /**
     * @var bool Whether to apply security headers
     */
    public $applySecurityHeaders = true;
    
    /**
     * @var bool Whether to check for brute force attempts
     */
    public $checkBruteForce = true;
    
    /**
     * @var bool Whether to log security events
     */
    public $logSecurityEvents = true;
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
            Controller::EVENT_AFTER_ACTION => 'afterAction',
        ];
    }
    
    /**
     * Before action security checks
     * @param \yii\base\ActionEvent $event
     */
    public function beforeAction($event)
    {
        $action = $event->action->id;
        
        // Skip security checks for certain actions
        if (in_array($action, $this->skipSecurityActions)) {
            return;
        }
        
        // Check for brute force attempts on login actions
        if ($action === 'login' && $this->checkBruteForce) {
            $this->checkBruteForceProtection();
        }
        
        // Validate CSRF token for POST requests
        if (Yii::$app->request->isPost && !in_array($action, $this->skipSecurityActions)) {
            $this->validateCsrfToken();
        }
        
        // Check for suspicious requests
        $this->checkSuspiciousRequests();
        
        // Log security event
        if ($this->logSecurityEvents) {
            $this->logSecurityEvent('action_access', [
                'action' => $action,
                'method' => Yii::$app->request->method,
                'url' => Yii::$app->request->url,
            ]);
        }
    }
    
    /**
     * After action security measures
     * @param \yii\base\ActionEvent $event
     */
    public function afterAction($event)
    {
        if ($this->applySecurityHeaders) {
            $this->applySecurityHeaders();
        }
    }
    
    /**
     * Check for brute force attempts
     */
    protected function checkBruteForceProtection()
    {
        $username = Yii::$app->request->post('LoginForm')['username'] ?? '';
        
        if (!empty($username)) {
            $securityMiddleware = new SecurityMiddleware();
            
            if (!$securityMiddleware->checkBruteForceAttempts($username)) {
                $this->logSecurityEvent('brute_force_attempt', ['username' => $username]);
                throw new ForbiddenHttpException('Account temporarily locked due to multiple failed login attempts.');
            }
        }
    }
    
    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken()
    {
        if (!Yii::$app->request->validateCsrfToken()) {
            $this->logSecurityEvent('csrf_violation', [
                'token' => Yii::$app->request->getCsrfToken(),
                'url' => Yii::$app->request->url,
            ]);
            throw new BadRequestHttpException('Invalid CSRF token.');
        }
    }
    
    /**
     * Check for suspicious requests
     */
    protected function checkSuspiciousRequests()
    {
        $request = Yii::$app->request;
        
        // Check for SQL injection attempts
        $suspiciousPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/<script/i',
            '/javascript:/i',
            '/onload=/i',
            '/onerror=/i',
        ];
        
        $url = $request->url;
        $postData = $request->post();
        $getData = $request->get();
        
        $allData = array_merge($postData, $getData);
        
        foreach ($suspiciousPatterns as $pattern) {
            foreach ($allData as $key => $value) {
                if (is_string($value) && preg_match($pattern, $value)) {
                    $this->logSecurityEvent('suspicious_request', [
                        'pattern' => $pattern,
                        'value' => $value,
                        'url' => $url,
                    ]);
                    throw new BadRequestHttpException('Suspicious request detected.');
                }
            }
        }
        
        // Check for XSS attempts
        $xssPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
        ];
        
        foreach ($xssPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                $this->logSecurityEvent('xss_attempt', [
                    'pattern' => $pattern,
                    'url' => $url,
                ]);
                throw new BadRequestHttpException('XSS attempt detected.');
            }
        }
    }
    
    /**
     * Apply security headers
     */
    protected function applySecurityHeaders()
    {
        $securityMiddleware = new SecurityMiddleware();
        $securityMiddleware->applySecurityHeaders(Yii::$app->response);
    }
    
    /**
     * Log security event
     * @param string $event
     * @param array $data
     */
    protected function logSecurityEvent($event, $data = [])
    {
        $securityMiddleware = new SecurityMiddleware();
        $securityMiddleware->logSecurityEvent($event, $data);
    }
} 