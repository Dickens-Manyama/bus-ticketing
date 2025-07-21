<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\web\Response;
use yii\web\Request;

/**
 * Security Middleware Component
 * Handles security headers, rate limiting, and other security measures
 */
class SecurityMiddleware extends Component
{
    /**
     * Apply security headers to response
     * @param Response $response
     */
    public function applySecurityHeaders($response)
    {
        $headers = $response->headers;
        
        // Security Headers
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('X-XSS-Protection', '1; mode=block');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' data:; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none';";
        
        $headers->set('Content-Security-Policy', $csp);
        
        // Remove server information
        $headers->remove('Server');
        $headers->remove('X-Powered-By');
    }
    
    /**
     * Validate file upload
     * @param \yii\web\UploadedFile $file
     * @return bool
     */
    public function validateFileUpload($file)
    {
        $securityParams = Yii::$app->params['security'] ?? [];
        $allowedTypes = $securityParams['allowedFileTypes'] ?? ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $maxFileSize = $securityParams['maxFileSize'] ?? 5 * 1024 * 1024; // 5MB
        
        // Check file size
        if ($file->size > $maxFileSize) {
            return false;
        }
        
        // Check file extension
        $extension = strtolower($file->extension);
        if (!in_array($extension, $allowedTypes)) {
            return false;
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->tempName);
        finfo_close($finfo);
        
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
        ];
        
        return in_array($mimeType, $allowedMimeTypes);
    }
    
    /**
     * Check for brute force attempts
     * @param string $username
     * @return bool
     */
    public function checkBruteForceAttempts($username)
    {
        $cache = Yii::$app->cache;
        $key = "login_attempts_{$username}";
        $attempts = $cache->get($key) ?: 0;
        
        $securityParams = Yii::$app->params['security'] ?? [];
        $maxAttempts = $securityParams['maxLoginAttempts'] ?? 5;
        $lockoutDuration = $securityParams['lockoutDuration'] ?? 900;
        
        if ($attempts >= $maxAttempts) {
            return false; // Account is locked
        }
        
        return true;
    }
    
    /**
     * Record failed login attempt
     * @param string $username
     */
    public function recordFailedAttempt($username)
    {
        $cache = Yii::$app->cache;
        $key = "login_attempts_{$username}";
        $attempts = $cache->get($key) ?: 0;
        $attempts++;
        
        $securityParams = Yii::$app->params['security'] ?? [];
        $lockoutDuration = $securityParams['lockoutDuration'] ?? 900;
        
        $cache->set($key, $attempts, $lockoutDuration);
    }
    
    /**
     * Clear failed login attempts
     * @param string $username
     */
    public function clearFailedAttempts($username)
    {
        $cache = Yii::$app->cache;
        $key = "login_attempts_{$username}";
        $cache->delete($key);
    }
    
    /**
     * Validate password strength
     * @param string $password
     * @return array
     */
    public function validatePasswordStrength($password)
    {
        $securityParams = Yii::$app->params['security'] ?? [];
        $minLength = $securityParams['passwordMinLength'] ?? 12;
        $requireUppercase = $securityParams['passwordRequireUppercase'] ?? true;
        $requireLowercase = $securityParams['passwordRequireLowercase'] ?? true;
        $requireNumbers = $securityParams['passwordRequireNumbers'] ?? true;
        $requireSpecialChars = $securityParams['passwordRequireSpecialChars'] ?? true;
        
        $errors = [];
        
        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long.";
        }
        
        if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        
        if ($requireLowercase && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        
        if ($requireNumbers && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        }
        
        if ($requireSpecialChars && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
    
    /**
     * Sanitize user input
     * @param string $input
     * @return string
     */
    public function sanitizeInput($input)
    {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);
        
        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        
        // HTML encode
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public function generateSecureToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Log security event
     * @param string $event
     * @param array $data
     */
    public function logSecurityEvent($event, $data = [])
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
            'user_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->id,
            'data' => $data,
        ];
        
        Yii::info(json_encode($logData), 'security');
    }
} 