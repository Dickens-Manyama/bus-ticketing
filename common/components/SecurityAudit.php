<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\web\User;

/**
 * Security Audit Component
 * Monitors and audits security events in the system
 */
class SecurityAudit extends Component
{
    /**
     * @var array Security events to audit
     */
    public $auditEvents = [
        'login_attempt',
        'failed_login',
        'successful_login',
        'logout',
        'password_change',
        'password_reset',
        'user_creation',
        'user_deletion',
        'role_change',
        'file_upload',
        'suspicious_activity',
        'admin_action',
        'data_access',
        'configuration_change',
    ];
    
    /**
     * @var string Log category for security events
     */
    public $logCategory = 'security';
    
    /**
     * @var bool Whether to store audit logs in database
     */
    public $storeInDatabase = true;
    
    /**
     * @var bool Whether to send alerts for critical events
     */
    public $sendAlerts = false;
    
    /**
     * Log a security event
     * @param string $event
     * @param array $data
     * @param string $level
     */
    public function logEvent($event, $data = [], $level = 'info')
    {
        if (!in_array($event, $this->auditEvents)) {
            return;
        }
        
        $auditData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'level' => $level,
            'ip_address' => Yii::$app->request->userIP,
            'user_agent' => Yii::$app->request->userAgent,
            'user_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->id,
            'username' => Yii::$app->user->isGuest ? null : Yii::$app->user->identity->username,
            'session_id' => Yii::$app->session->id,
            'url' => Yii::$app->request->url,
            'method' => Yii::$app->request->method,
            'data' => $data,
        ];
        
        // Log to file
        Yii::$level(json_encode($auditData), $this->logCategory);
        
        // Store in database if enabled
        if ($this->storeInDatabase) {
            $this->storeInDatabase($auditData);
        }
        
        // Send alert for critical events
        if ($this->sendAlerts && $this->isCriticalEvent($event)) {
            $this->sendAlert($event, $auditData);
        }
    }
    
    /**
     * Store audit log in database
     * @param array $auditData
     */
    protected function storeInDatabase($auditData)
    {
        try {
            // You can create a dedicated audit log table
            // For now, we'll just log to the application log
            Yii::info('Security Audit: ' . json_encode($auditData), 'audit');
        } catch (\Exception $e) {
            Yii::error('Failed to store audit log: ' . $e->getMessage(), 'audit');
        }
    }
    
    /**
     * Check if event is critical
     * @param string $event
     * @return bool
     */
    protected function isCriticalEvent($event)
    {
        $criticalEvents = [
            'failed_login',
            'suspicious_activity',
            'admin_action',
            'configuration_change',
        ];
        
        return in_array($event, $criticalEvents);
    }
    
    /**
     * Send security alert
     * @param string $event
     * @param array $auditData
     */
    protected function sendAlert($event, $auditData)
    {
        // Implementation for sending alerts (email, SMS, etc.)
        // This is a placeholder - implement based on your needs
        Yii::warning("Security Alert: {$event} - " . json_encode($auditData), 'security');
    }
    
    /**
     * Audit user login attempt
     * @param string $username
     * @param bool $success
     * @param string $reason
     */
    public function auditLoginAttempt($username, $success, $reason = '')
    {
        $event = $success ? 'successful_login' : 'failed_login';
        $level = $success ? 'info' : 'warning';
        
        $data = [
            'username' => $username,
            'success' => $success,
            'reason' => $reason,
        ];
        
        $this->logEvent($event, $data, $level);
    }
    
    /**
     * Audit user logout
     * @param string $username
     */
    public function auditLogout($username)
    {
        $data = [
            'username' => $username,
        ];
        
        $this->logEvent('logout', $data, 'info');
    }
    
    /**
     * Audit password change
     * @param int $userId
     * @param string $username
     */
    public function auditPasswordChange($userId, $username)
    {
        $data = [
            'user_id' => $userId,
            'username' => $username,
        ];
        
        $this->logEvent('password_change', $data, 'info');
    }
    
    /**
     * Audit file upload
     * @param string $filename
     * @param string $type
     * @param int $size
     * @param bool $validated
     */
    public function auditFileUpload($filename, $type, $size, $validated)
    {
        $data = [
            'filename' => $filename,
            'type' => $type,
            'size' => $size,
            'validated' => $validated,
        ];
        
        $level = $validated ? 'info' : 'warning';
        $event = $validated ? 'file_upload' : 'suspicious_activity';
        
        $this->logEvent($event, $data, $level);
    }
    
    /**
     * Audit admin action
     * @param string $action
     * @param array $params
     * @param string $username
     */
    public function auditAdminAction($action, $params, $username)
    {
        $data = [
            'action' => $action,
            'params' => $params,
            'username' => $username,
        ];
        
        $this->logEvent('admin_action', $data, 'info');
    }
    
    /**
     * Audit suspicious activity
     * @param string $activity
     * @param array $details
     */
    public function auditSuspiciousActivity($activity, $details = [])
    {
        $data = [
            'activity' => $activity,
            'details' => $details,
        ];
        
        $this->logEvent('suspicious_activity', $data, 'warning');
    }
    
    /**
     * Audit data access
     * @param string $table
     * @param string $operation
     * @param array $criteria
     */
    public function auditDataAccess($table, $operation, $criteria = [])
    {
        $data = [
            'table' => $table,
            'operation' => $operation,
            'criteria' => $criteria,
        ];
        
        $this->logEvent('data_access', $data, 'info');
    }
    
    /**
     * Get security statistics
     * @param string $period
     * @return array
     */
    public function getSecurityStats($period = '24h')
    {
        // Implementation to get security statistics
        // This is a placeholder - implement based on your needs
        return [
            'total_events' => 0,
            'failed_logins' => 0,
            'suspicious_activities' => 0,
            'admin_actions' => 0,
        ];
    }
    
    /**
     * Generate security report
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function generateSecurityReport($startDate, $endDate)
    {
        // Implementation to generate security reports
        // This is a placeholder - implement based on your needs
        return [
            'period' => "{$startDate} to {$endDate}",
            'total_events' => 0,
            'events_by_type' => [],
            'top_ips' => [],
            'top_users' => [],
        ];
    }
    
    /**
     * Clean old audit logs
     * @param int $daysToKeep
     */
    public function cleanOldLogs($daysToKeep = 90)
    {
        // Implementation to clean old audit logs
        // This is a placeholder - implement based on your needs
        Yii::info("Cleaning audit logs older than {$daysToKeep} days", 'audit');
    }
} 