<?php

/**
 * Security Configuration
 * Comprehensive security settings for the Bus Ticketing System
 */

return [
    // Password Policy
    'passwordPolicy' => [
        'minLength' => 12,
        'requireUppercase' => true,
        'requireLowercase' => true,
        'requireNumbers' => true,
        'requireSpecialChars' => true,
        'maxAge' => 90 * 24 * 3600, // 90 days
        'historyCount' => 5, // Remember last 5 passwords
    ],
    
    // Session Security
    'sessionSecurity' => [
        'timeout' => 1800, // 30 minutes
        'absoluteTimeout' => 3600, // 1 hour
        'regenerateId' => true,
        'useStrictMode' => true,
        'useOnlyCookies' => true,
        'cookieParams' => [
            'httponly' => true,
            'secure' => false, // Set to true in production with HTTPS
            'samesite' => 'Lax',
            'path' => '/',
        ],
    ],
    
    // Brute Force Protection
    'bruteForceProtection' => [
        'maxAttempts' => 5,
        'lockoutDuration' => 900, // 15 minutes
        'trackByIp' => true,
        'trackByUsername' => true,
        'incrementalLockout' => true,
    ],
    
    // File Upload Security
    'fileUploadSecurity' => [
        'allowedTypes' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
        'maxFileSize' => 5 * 1024 * 1024, // 5MB
        'scanForViruses' => false, // Enable if antivirus is available
        'validateMimeType' => true,
        'allowedMimeTypes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
        ],
    ],
    
    // CSRF Protection
    'csrfProtection' => [
        'enabled' => true,
        'tokenExpire' => 3600, // 1 hour
        'validateToken' => true,
        'regenerateToken' => true,
    ],
    
    // XSS Protection
    'xssProtection' => [
        'enabled' => true,
        'mode' => 'block',
        'sanitizeInput' => true,
        'sanitizeOutput' => true,
    ],
    
    // SQL Injection Protection
    'sqlInjectionProtection' => [
        'enabled' => true,
        'usePreparedStatements' => true,
        'validateInput' => true,
        'escapeOutput' => true,
    ],
    
    // Security Headers
    'securityHeaders' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'none';",
    ],
    
    // Rate Limiting
    'rateLimiting' => [
        'enabled' => true,
        'requestsPerMinute' => 60,
        'requestsPerHour' => 1000,
        'requestsPerDay' => 10000,
        'trackByIp' => true,
        'trackByUser' => true,
    ],
    
    // Logging and Monitoring
    'securityLogging' => [
        'enabled' => true,
        'logLevel' => 'info',
        'logEvents' => [
            'login_attempts',
            'failed_logins',
            'successful_logins',
            'logout_events',
            'password_changes',
            'suspicious_activity',
            'file_uploads',
            'admin_actions',
        ],
        'retentionDays' => 90,
    ],
    
    // IP Whitelist/Blacklist
    'ipFiltering' => [
        'enabled' => false,
        'whitelist' => [],
        'blacklist' => [],
        'blockSuspiciousIps' => true,
    ],
    
    // Two-Factor Authentication
    'twoFactorAuth' => [
        'enabled' => false,
        'requiredForAdmins' => true,
        'requiredForUsers' => false,
        'methods' => ['totp', 'sms', 'email'],
    ],
    
    // API Security
    'apiSecurity' => [
        'enabled' => true,
        'requireApiKey' => true,
        'rateLimit' => true,
        'validateOrigin' => true,
        'allowedOrigins' => [],
    ],
    
    // Database Security
    'databaseSecurity' => [
        'usePreparedStatements' => true,
        'escapeOutput' => true,
        'validateInput' => true,
        'connectionEncryption' => false, // Enable for production
        'queryLogging' => false, // Enable for debugging
    ],
    
    // Email Security
    'emailSecurity' => [
        'validateEmailAddresses' => true,
        'preventEmailInjection' => true,
        'useSpf' => true,
        'useDkim' => false,
        'useDmarc' => false,
    ],
    
    // Backup Security
    'backupSecurity' => [
        'encryptBackups' => false, // Enable for production
        'secureBackupLocation' => true,
        'validateBackupIntegrity' => true,
        'backupRetention' => 30, // days
    ],
    
    // Development Security
    'developmentSecurity' => [
        'hideErrorDetails' => true,
        'disableDebugMode' => true,
        'secureConfiguration' => true,
        'validateEnvironment' => true,
    ],
]; 