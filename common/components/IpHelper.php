<?php
namespace common\components;

class IpHelper
{
    /**
     * Get the current server's local IP address (for LAN/WiFi use)
     * @return string
     */
    public static function getServerIp()
    {
        // Use the specific WiFi IP address for QR codes
        return '192.168.100.76';
    }

    /**
     * Get the current server's port (for dynamic URL generation)
     * @return int
     */
    public static function getServerPort()
    {
        if (isset($_SERVER['SERVER_PORT'])) {
            return (int)$_SERVER['SERVER_PORT'];
        }
        return 8080; // Default to port 8080 for XAMPP
    }

    /**
     * Get the full server URL for QR codes
     * @return string
     */
    public static function getServerUrl()
    {
        $ip = self::getServerIp();
        $port = self::getServerPort();
        return "http://{$ip}:{$port}";
    }
} 