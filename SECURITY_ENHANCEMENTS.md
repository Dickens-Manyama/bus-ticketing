# Security Enhancements for Bus Ticketing System

## Overview
This document outlines the comprehensive security enhancements implemented in the Bus Ticketing System to protect against various security threats and vulnerabilities.

## 🔒 Security Components Implemented

### 1. Security Middleware (`common/components/SecurityMiddleware.php`)
**Purpose**: Centralized security operations and utilities

**Features**:
- Security headers management
- File upload validation
- Brute force protection
- Password strength validation
- Input sanitization
- Secure token generation
- Security event logging

**Key Methods**:
- `applySecurityHeaders()` - Applies comprehensive security headers
- `validateFileUpload()` - Validates uploaded files for security
- `checkBruteForceAttempts()` - Prevents brute force attacks
- `validatePasswordStrength()` - Enforces strong password policy
- `sanitizeInput()` - Sanitizes user input
- `generateSecureToken()` - Generates cryptographically secure tokens

### 2. Security Behavior (`common/behaviors/SecurityBehavior.php`)
**Purpose**: Automatically applies security measures to controllers

**Features**:
- Automatic security header application
- Brute force protection on login actions
- CSRF token validation
- Suspicious request detection
- Security event logging
- SQL injection prevention
- XSS attack prevention

**Security Checks**:
- SQL injection patterns detection
- XSS attack patterns detection
- Suspicious URL patterns
- Malicious input validation

### 3. Security Audit Component (`common/components/SecurityAudit.php`)
**Purpose**: Comprehensive security event monitoring and auditing

**Features**:
- Security event logging
- Audit trail maintenance
- Critical event alerts
- Security statistics
- Security reports generation
- Log retention management

**Audited Events**:
- Login attempts (successful/failed)
- Logout events
- Password changes
- File uploads
- Admin actions
- Suspicious activities
- Data access patterns

## 🛡️ Security Configuration

### Enhanced Database Security
```php
'db' => [
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'enableQueryCache' => true,
    'queryCacheDuration' => 1800,
    'attributes' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ],
],
```

### Session Security
```php
'session' => [
    'timeout' => 1800, // 30 minutes
    'cookieParams' => [
        'httponly' => true,
        'secure' => false, // Set to true in production with HTTPS
        'samesite' => 'Lax',
    ],
    'useCookies' => true,
    'gCProbability' => 1,
],
```

### User Authentication Security
```php
'user' => [
    'enableAutoLogin' => false, // Disabled for security
    'authTimeout' => 1800, // 30 minutes
    'absoluteAuthTimeout' => 3600, // 1 hour
],
```

## 🔐 Security Headers Implemented

### Content Security Policy (CSP)
```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval';
style-src 'self' 'unsafe-inline';
img-src 'self' data: https:;
font-src 'self' data:;
connect-src 'self';
frame-ancestors 'none';
```

### Other Security Headers
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: geolocation=(), microphone=(), camera=()`

## 🚫 Brute Force Protection

### Configuration
- **Max Attempts**: 5 failed login attempts
- **Lockout Duration**: 15 minutes
- **Tracking**: By username and IP address
- **Incremental Lockout**: Enabled

### Implementation
```php
// Check for brute force attempts
if (!$securityMiddleware->checkBruteForceAttempts($username)) {
    $securityMiddleware->logSecurityEvent('brute_force_blocked', ['username' => $username]);
    throw new ForbiddenHttpException('Account temporarily locked due to multiple failed login attempts.');
}
```

## 🔑 Password Security

### Password Policy
- **Minimum Length**: 12 characters
- **Requirements**:
  - At least one uppercase letter
  - At least one lowercase letter
  - At least one number
  - At least one special character
- **Hash Cost**: 12 (increased from default 10)

### Password Validation
```php
$validation = $securityMiddleware->validatePasswordStrength($password);
if (!$validation['valid']) {
    // Handle password strength errors
}
```

## 📁 File Upload Security

### Allowed File Types
- Images: JPG, JPEG, PNG, GIF
- Documents: PDF
- Maximum Size: 5MB

### Validation Process
1. File size validation
2. File extension validation
3. MIME type validation
4. Content scanning (if antivirus available)

## 🛡️ Input Validation & Sanitization

### SQL Injection Prevention
- Prepared statements usage
- Input validation patterns
- Suspicious pattern detection

### XSS Prevention
- Input sanitization
- Output encoding
- Content Security Policy
- XSS protection headers

### Suspicious Pattern Detection
```php
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
```

## 📊 Security Monitoring & Logging

### Logged Events
- Login attempts (successful/failed)
- Logout events
- Password changes
- File uploads
- Admin actions
- Suspicious activities
- Data access patterns

### Log Format
```json
{
    "timestamp": "2024-01-01 12:00:00",
    "event": "failed_login",
    "level": "warning",
    "ip_address": "192.168.1.1",
    "user_agent": "Mozilla/5.0...",
    "user_id": null,
    "username": "testuser",
    "session_id": "abc123",
    "url": "/site/login",
    "method": "POST",
    "data": {
        "username": "testuser",
        "success": false,
        "reason": "Invalid password"
    }
}
```

## 🔍 Security Audit Features

### Audit Capabilities
- Real-time security event monitoring
- Comprehensive audit trails
- Security statistics generation
- Critical event alerts
- Log retention management

### Security Reports
- Event type analysis
- Top IP addresses
- Top users
- Time-based patterns
- Suspicious activity detection

## 🚨 Security Alerts

### Critical Events
- Failed login attempts
- Suspicious activities
- Admin actions
- Configuration changes

### Alert Mechanisms
- Log-based alerts
- Email notifications (configurable)
- SMS notifications (configurable)
- Dashboard notifications

## 🔧 Implementation in Controllers

### Backend SiteController
```php
public function behaviors()
{
    return [
        'security' => [
            'class' => SecurityBehavior::class,
            'checkBruteForce' => true,
            'logSecurityEvents' => true,
            'skipSecurityActions' => ['error', 'captcha', 'test'],
        ],
        // ... other behaviors
    ];
}
```

### Frontend SiteController
Similar security behavior implementation with role-based access control.

## 📋 Security Checklist

### ✅ Implemented Security Measures
- [x] Brute force protection
- [x] Strong password policy
- [x] Session security
- [x] CSRF protection
- [x] XSS prevention
- [x] SQL injection prevention
- [x] File upload validation
- [x] Security headers
- [x] Input sanitization
- [x] Security logging
- [x] Audit trails
- [x] Rate limiting
- [x] Suspicious activity detection

### 🔄 Recommended Additional Measures
- [ ] Two-factor authentication (2FA)
- [ ] SSL/TLS encryption
- [ ] Database encryption
- [ ] Backup encryption
- [ ] IP whitelisting/blacklisting
- [ ] Advanced threat detection
- [ ] Security scanning tools
- [ ] Penetration testing

## 🚀 Usage Instructions

### 1. Enable Security Components
The security components are automatically enabled when you include the security behavior in your controllers.

### 2. Monitor Security Logs
Check the application logs for security events:
```bash
tail -f runtime/logs/app.log | grep security
```

### 3. Review Security Reports
Use the SecurityAudit component to generate security reports:
```php
$audit = new SecurityAudit();
$report = $audit->generateSecurityReport('2024-01-01', '2024-01-31');
```

### 4. Configure Alerts
Enable security alerts in production:
```php
'components' => [
    'securityAudit' => [
        'class' => 'common\components\SecurityAudit',
        'sendAlerts' => true,
    ],
],
```

## 🔒 Production Security Recommendations

### 1. Enable HTTPS
- Configure SSL/TLS certificates
- Set `secure` => true in session configuration
- Use HTTPS for all communications

### 2. Database Security
- Use strong database passwords
- Enable database encryption
- Implement connection encryption
- Regular database backups

### 3. Server Security
- Keep server software updated
- Configure firewall rules
- Implement intrusion detection
- Regular security audits

### 4. Application Security
- Regular security updates
- Penetration testing
- Code security reviews
- Vulnerability scanning

## 📞 Security Support

For security-related issues or questions:
1. Check the security logs for detailed information
2. Review the security configuration files
3. Monitor the audit trails for suspicious activities
4. Contact the development team for security concerns

---

**Last Updated**: January 2024
**Version**: 1.0
**Security Level**: Enhanced 